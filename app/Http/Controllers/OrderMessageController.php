<?php

namespace Mss\Http\Controllers;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Mss\DataTables\AssignOrderDataTable;
use Mss\DataTables\OrderDataTable;
use Mss\Http\Requests\NewOrderMessageRequest;
use Mss\Mail\SupplierMail;
use Mss\Models\Order;
use Mss\Models\OrderMessage;
use Webpatser\Uuid\Uuid;

class OrderMessageController extends Controller {

    public function create(Order $order) {
        $order->load(['items.article' => function ($query) {
            $query->withCurrentSupplierArticle();
        }, 'items.article.unit']);

        $preSetBody = null;
        $preSetReceiver = null;
        $preSetSubject = null;

        if (request('answer')) {
            $orgMessage = OrderMessage::find(request('answer'));
            $preSetSubject = 'RE: '.$orgMessage->subject;
            $preSetReceiver = $orgMessage->sender->contains('System') ? '' : $orgMessage->sender->implode(',');
            $preSetBody = '<br/><br/>Am '.$orgMessage->received->formatLocalized('%A, %d.%B %Y, %H:%M Uhr').' schrieb '.($orgMessage->sender->contains('System') ? env('MAIL_FROM_ADDRESS') : $orgMessage->sender->first()).':<br/><blockquote style="padding: 10px 20px;margin: 5px 0 20px;border-left: 5px solid #eee;">'.$orgMessage->htmlBody.'</blockquote>';
        }

        $sendOrder = false;
        if (request('sendorder')) {
            $preSetBody = view('emails.new_order', compact('order'))->render();
            $preSetSubject = '['.$order->internal_order_number.'] Neue Bestellung';
            $sendOrder = true;
        }

        return view('order_messages.create', compact('order', 'preSetBody', 'preSetReceiver', 'preSetSubject', 'sendOrder'));
    }

    /**
     * @param Order $order
     * @param NewOrderMessageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Order $order, NewOrderMessageRequest $request) {
        $attachments = collect(json_decode($request->get('attachments'), true));
        $attachments->transform(function ($attachment) {
            $fileName = Uuid::generate(4)->string;
            if (rename(storage_path('app/'.$attachment['tempFile']), storage_path('attachments/'.$fileName))) {
                return [
                    'fileName' => $fileName,
                    'contentType' => $attachment['type'],
                    'orgFileName' => $attachment['orgName']
                ];
            }
        });

        $receivers = collect(explode(',', $request->get('receiver')))->transform(function ($receiver) {
            return trim($receiver);
        });

        if (count($receivers) > 1) {
            $mail = Mail::to($receivers->first())->cc($receivers->slice(1));
        } else {
            $mail = Mail::to($receivers);
        }

        $mail->queue(new SupplierMail (
            $request->get('subject'), $request->get('body'), $attachments
        ));

        $order->messages()->create([
            'user_id' => Auth::id(),
            'sender' => ['System'],
            'receiver' => $receivers,
            'subject' => $request->get('subject'),
            'htmlBody' => $request->get('body'),
            'attachments' => $attachments,
            'read' => true,
            'received' => Carbon::now()
        ]);

        if ($request->get('sendOrder')) {
            $order->status = Order::STATUS_ORDERED;
            $order->save();
        }

        flash('Nachricht verschickt')->success();

        return redirect()->route('order.show', $order);
    }

    public function delete(OrderMessage $message, $order = null) {
        $message->delete();

        flash('Nachricht gelöscht')->success();

        if ($order = Order::find($order)) {
            return redirect()->route('order.show', $order);
        } else {
            return redirect()->route('order.messages_unassigned');
        }
    }

    public function markUnread(Order $order, OrderMessage $message) {
        $message->read = false;
        $message->save();

        return redirect()->route('order.show', $order);
    }

    public function markRead(Order $order, OrderMessage $message) {
        $message->read = true;
        $message->save();

        return redirect()->route('order.show', $order);
    }

    public function uploadNewAttachments(Order $order, Request $request) {
        $file = $request->file('file');

        /**
         * @todo queue file to delete after some time
         */
        $upload_success = $file->storeAs('upload_temp', $order->id.'_'.Uuid::generate(4)->string);
        if ($upload_success) {
            return response()->json($upload_success, 200);
        } else {
            return response()->json('error', 400);
        }
    }

    public function unassignedMessages(AssignOrderDataTable $assignOrderDataTable) {
        $unassignedMessages = OrderMessage::unassigned()->unread()->get();

        return $assignOrderDataTable->render('order_messages.unsassigned_messages', compact('unassignedMessages'));
    }

    public function messageAttachmentDownload(OrderMessage $message, $attachment) {
        $attachment = $message->attachments->where('fileName', $attachment)->first();
        $filePath = storage_path('attachments/'.$attachment['fileName']);
        if (!file_exists($filePath)) {
            $filePath = storage_path('app/attachments/'.$attachment['fileName']);
            if (!file_exists($filePath)) {
                return response("Datei nicht gefunden", 404);
            }
        }

        return response()->download($filePath, $attachment['orgFileName'], ['Content-Type' => $attachment['contentType']]);
    }

    public function assignToOrder(Request $request) {
        $message = OrderMessage::find($request->get('message'));
        $order = Order::find($request->get('orderid'));
        if ($message && $order) {
            $message->order()->associate($order);
            $message->save();
            flash('Nachricht verschoben')->success();
            return redirect()->route('order.show', $order);
        }

        flash('Nachricht nicht verschoben')->error();
        return redirect()->route('order.messages_unassigned');
    }

    public function forwardForm(OrderMessage $message) {
        $preSetBody = $message->htmlBody;
        $preSetReceiver = null;
        $preSetSubject = 'FW: '.$message->subject;

        return view('order_messages.forward', compact('preSetBody', 'preSetReceiver', 'preSetSubject', 'message'));
    }

    public function forward(OrderMessage $message, Request $request) {
        $receivers = collect(explode(',', $request->get('receiver')))->transform(function ($receiver) {
            return trim($receiver);
        });

        if (count($receivers) > 1) {
            $mail = Mail::to($receivers->first())->cc($receivers->slice(1));
        } else {
            $mail = Mail::to($receivers);
        }

        $body = (!empty($message->htmlBody)) ? $message->htmlBody : $message->textBody;
        $mail->queue(new SupplierMail (
            'FW '.$message->subject, $body, $message->attachments
        ));

        flash('Nachricht weitergeleitet')->success();

        return redirect()->back();
    }
}