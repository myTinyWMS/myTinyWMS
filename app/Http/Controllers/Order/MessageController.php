<?php

namespace Mss\Http\Controllers\Order;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Mss\DataTables\AssignOrderDataTable;
use Mss\DataTables\OrderDataTable;
use Mss\Http\Controllers\Controller;
use Mss\Http\Requests\NewOrderMessageRequest;
use Mss\Mail\SupplierMail;
use Mss\Models\Order;
use Mss\Models\OrderMessage;
use Webpatser\Uuid\Uuid;

class MessageController extends Controller {

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
            $body = (empty($orgMessage->htmlBody)) ? nl2br($orgMessage->textBody) : $orgMessage->htmlBody;
            $preSetBody = '<br/><br/>Am '.$orgMessage->received->formatLocalized('%A, %d.%B %Y, %H:%M Uhr').' schrieb '.($orgMessage->sender->contains('System') ? env('MAIL_FROM_ADDRESS') : $orgMessage->sender->first()).':<br/><blockquote style="padding: 10px 20px;margin: 5px 0 20px;border-left: 5px solid #eee;">'.$body.'</blockquote>';
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

        flash('Nachricht als ungelesen markiert.')->success();

        return redirect()->route('order.show', $order);
    }

    public function markRead(Order $order, OrderMessage $message) {
        $message->read = true;
        $message->save();

        flash('Nachricht als gelesen markiert.')->success();

        return redirect()->route('order.show', $order);
    }
}