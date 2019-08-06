<?php

namespace Mss\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Mss\DataTables\ToOrderDataTable;
use Mss\Models\Article;
use Mss\Models\Order;
use Mss\Models\OrderItem;

class DashboardController extends Controller
{
    public function index(ToOrderDataTable $toOrderDataTable) {
        if (!Auth::check()) {
            return response()->redirectToRoute('login');
        }

        $deliveriesWithoutInvoice = $this->getDeliveriesWithoutInvoice();
        $invoicesWithoutDelivery = $this->getInvoicesWithoutDelivery();
        $overdueOrders = Order::with(['items', 'supplier'])->overdue()->get();
        $ordersWithoutMessages = Order::with(['items', 'supplier'])->whereDoesntHave('messages')->whereIn('status', [Order::STATUS_NEW, Order::STATUS_ORDERED])->whereHas('supplier', function ($query) {
            $query->where('email', '!=', '');
        })->get();
        $ordersWithoutConfirmation = Order::with(['items', 'supplier'])->whereIn('status', [Order::STATUS_NEW, Order::STATUS_ORDERED])->whereHas('items', function ($query) {
            $query->where('confirmation_received', false);
        })->get();

        return $toOrderDataTable->render('dashboard', compact('deliveriesWithoutInvoice', 'invoicesWithoutDelivery', 'overdueOrders', 'ordersWithoutMessages', 'ordersWithoutConfirmation'));
    }

    protected function getDeliveriesWithoutInvoice() {
        return OrderItem::with(['order.supplier', 'article', 'deliveryItems'])->whereHas('order.deliveries')->where('invoice_received', 0)->get()->filter(function ($orderItem) {
            return ($orderItem->deliveryItems->sum('quantity') && $orderItem->article->inventory == Article::INVENTORY_TYPE_CONSUMABLES);
        });
    }

    protected function getInvoicesWithoutDelivery() {
        return OrderItem::with(['order', 'article'])->where('invoice_received', 1)->whereDoesntHave('order.deliveries')->get()->filter(function ($orderItem) {
            return ($orderItem->order->status !== Order::STATUS_CANCELLED);
        });
    }
}
