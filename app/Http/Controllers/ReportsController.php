<?php

namespace Mss\Http\Controllers;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Mss\Models\Order;
use Mss\Models\OrderItem;
use Mss\Services\InventoryService;

class ReportsController extends Controller
{
    public function index() {
        return view('reports.index');
    }

    public function generateInventoryPdf() {
        return InventoryService::generatePdf()->download('inventur_'.Carbon::now()->format('Y-m-d').'.pdf');
    }

    public function generateInventoryReport(Request $request) {
        return InventoryService::generateReport($request->get('month'));
    }

    public function deliveriesWithoutInvoice() {
        $openItems = OrderItem::with(['order', 'article'])->whereHas('order.deliveries')->where('invoice_received', 0)->get()->filter(function ($orderItem) {
            return $orderItem->deliveryItems->sum('quantity');
        });

        return view('reports.delivery_without_invoice', compact('openItems'));
    }
}
