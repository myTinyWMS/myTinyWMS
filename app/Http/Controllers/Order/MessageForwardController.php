<?php

namespace Mss\Http\Controllers\Order;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Mss\Http\Controllers\Controller;
use Mss\Mail\SupplierMail;
use Mss\Models\OrderMessage;

class MessageForwardController extends Controller
{
    public function create(OrderMessage $message) {
        $preSetBody = $message->htmlBody;
        $preSetReceiver = null;
        $preSetSubject = 'FW: '.$message->subject;

        return view('order_messages.forward', compact('preSetBody', 'preSetReceiver', 'preSetSubject', 'message'));
    }

    public function store(OrderMessage $message, Request $request) {
        $message->load('order');
        $receivers = collect(explode(',', $request->get('receiver')))->transform(function ($receiver) {
            return trim($receiver);
        });

        if (count($receivers) > 1) {
            $mail = Mail::to($receivers->first())->cc($receivers->slice(1));
        } else {
            $mail = Mail::to($receivers);
        }

        $body = $request->get('content');
        $mail->queue(new SupplierMail (
            'FW '.$message->subject, $body, $message->attachments
        ));

        flash('Nachricht weitergeleitet')->success();

        if ($message->order) {
            return response()->redirectToRoute('order.show', $message->order);
        } else {
            return response()->redirectToRoute('order.messages_unassigned');
        }
    }
}
