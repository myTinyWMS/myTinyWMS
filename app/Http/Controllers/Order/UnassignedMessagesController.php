<?php

namespace Mss\Http\Controllers\Order;

use Illuminate\Http\Request;
use Mss\DataTables\AssignOrderDataTable;
use Mss\Http\Controllers\Controller;
use Mss\Models\Order;
use Mss\Models\OrderMessage;

class UnassignedMessagesController extends Controller
{
    public function index(AssignOrderDataTable $assignOrderDataTable) {
        $unassignedMessages = OrderMessage::unassigned()->unread()->get();

        return $assignOrderDataTable->render('order_messages.unsassigned_messages', compact('unassignedMessages'));
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
}
