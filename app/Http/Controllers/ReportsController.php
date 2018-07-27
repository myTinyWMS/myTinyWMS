<?php

namespace Mss\Http\Controllers;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Mss\Exports\ArticleUsageReport;
use Mss\Models\Article;
use Mss\Models\Order;
use Mss\Models\OrderItem;
use Mss\Services\InventoryService;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends Controller
{
    public function index() {
        return view('reports.index');
    }

    public function generateInventoryPdf() {
        return InventoryService::generatePdf(Article::INVENTORY_TYPE_CONSUMABLES)->download('inventur_'.Carbon::now()->format('Y-m-d').'.pdf');
    }

    public function generateYearlyInventoryPdf() {
        return InventoryService::generatePdf()->download('jahres_inventur_'.Carbon::now()->format('Y-m-d').'.pdf');
    }

    public function generateInventoryReport(Request $request) {
        return InventoryService::generateReport($request->get('month'), $request->get('inventorytype'));
    }

    public function deliveriesWithoutInvoice() {
        $openItems = OrderItem::with(['order', 'article'])->whereHas('order.deliveries')->where('invoice_received', 0)->get()->filter(function ($orderItem) {
            return $orderItem->deliveryItems->sum('quantity');
        });

        return view('reports.delivery_without_invoice', compact('openItems'));
    }

    public function invoicesWithoutDelivery() {
        $openItems = OrderItem::with(['order', 'article'])->where('invoice_received', 1)->whereDoesntHave('order.deliveries')->get();

        return view('reports.invoices_without_delivery', compact('openItems'));
    }

    public function generateArticleUsageReport(Request $request) {
        return Excel::download(new ArticleUsageReport($request->get('month')), 'article_usage_report_'.$request->get('month').'.xlsx');
    }
}
