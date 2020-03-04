<?php

namespace Mss\Http\Controllers\Order;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Mss\Http\Controllers\Controller;
use Mss\Mail\InvoiceCheckMail;
use Mss\Models\Order;
use Mss\Models\OrderItem;
use Mss\Models\UserSettings;

class OrderItemsController extends Controller
{
    public function confirmationReceived(OrderItem $orderitem, $status) {
        $orderitem->confirmation_received = ($status == 1);
        $orderitem->save();

        flash(__('Status der Auftragsbestätigung aktualisiert.'))->success();

        return redirect()->route('order.show', $orderitem->order);
    }

    public function invoiceReceived(OrderItem $orderitem, Request $request) {
        $request->validate([
            'invoice_status' => 'required|in:0,1,2',
            'change_article_price' => 'in:0,1'
        ]);

        $orderitem->invoice_received = $request->get('invoice_status');
        $orderitem->save();

        if (request('change_article_price') == 1) {
            $supplierArticle = $orderitem->article->getCurrentSupplierArticle();
            $supplierArticle->price = $orderitem->price * 100;
            $supplierArticle->save();
        }

        if (!empty($request->get('mail_note'))) {
            $attachments = collect($request->get('mail_attachments'));
            Mail::to(UserSettings::getUsersWhereTrue(UserSettings::SETTING_NOTIFY_ON_INVOICE_CHECKS))->send(new InvoiceCheckMail($orderitem->order, nl2br($request->get('mail_note')), $attachments));
        }

        flash(__('Status der Rechnung aktualisiert.'))->success();

        return 'true';
    }

    public function allItemsInvoiceReceived(Order $order) {
        $order->items->each(function ($orderitem) {
            if ($orderitem->invoice_received !== OrderItem::INVOICE_STATUS_RECEIVED) {
                $orderitem->invoice_received = OrderItem::INVOICE_STATUS_RECEIVED;
                $orderitem->save();
            }

            if (request('change_article_price') == 1) {
                $supplierArticle = $orderitem->article->getCurrentSupplierArticle();
                $supplierArticle->price = $orderitem->price * 100;
                $supplierArticle->save();
            }
        });

        return redirect()->route('order.show', $order);
    }

    public function allItemsConfirmationReceived(Order $order) {
        $order->items->each(function ($orderitem) {
            if (!$orderitem->confirmation_received) {
                $orderitem->confirmation_received = true;
                $orderitem->save();
            }
        });

        return redirect()->route('order.show', $order);
    }
}
