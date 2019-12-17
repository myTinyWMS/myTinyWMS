<?php

namespace Mss\Http\Controllers;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Mss\Exports\ArticleUsageReport;
use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;
use Mss\Models\Delivery;
use Mss\Models\Order;
use Mss\Models\OrderItem;
use Mss\Services\InventoryService;
use Maatwebsite\Excel\Facades\Excel;
use Mss\Services\ReportService;

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
        $openItems = OrderItem::with(['order.supplier', 'article'])
            ->whereHas('order.deliveries')
            ->where('invoice_received', 0)

            ->get()
            ->filter(function ($orderItem) {
                return ($orderItem->deliveryItems->sum('quantity') && $orderItem->article->inventory == Article::INVENTORY_TYPE_CONSUMABLES);
            });

        return view('reports.delivery_without_invoice', compact('openItems'));
    }

    public function deliveriesWithInvoice(Request $request) {
        $category = intval($request->get('category', 0));
        $month = $request->get('month');
        $start = Carbon::parse($month.'-01');
        $end = $start->copy()->endOfMonth();

        $report = new ReportService();
        $items = $report->getInvoicesWithDeliveries($start, $end, $category);

        return view('reports.delivery_with_invoice', compact('items', 'start', 'month', 'category'));
    }

    public function deliveriesWithInvoiceExport(Request $request) {
        $category = intval($request->get('category', 0));
        $month = $request->get('month');
        $start = Carbon::parse($month.'-01');
        $end = $start->copy()->endOfMonth();

        $report = new ReportService();
        $items = $report->getInvoicesWithDeliveries($start, $end, $category);

        return response(view('reports.export.delivery_with_invoice', compact('items')), 200, [
            'Content-Type' => 'application/octet-stream', // use your required mime type
            'Content-Disposition' => 'attachment; filename="report.csv"',
        ]);
    }

    public function invoicesWithoutDelivery() {
        $openItems = OrderItem::with(['order', 'article'])->where('invoice_received', 1)->whereDoesntHave('order.deliveries')->get()->filter(function ($orderItem) {
            return ($orderItem->order->status !== Order::STATUS_CANCELLED);
        });

        return view('reports.invoices_without_delivery', compact('openItems'));
    }

    public function generateArticleUsageReport(Request $request) {
        return Excel::download(new ArticleUsageReport($request->get('month')), 'article_usage_report_'.$request->get('month').'.xlsx');
    }

    public function generateArticleWeightReport(Request $request) {
        $dateRange = explode(',', $request->get('daterange'));
        $start = Carbon::parse($dateRange[0]);
        $end = Carbon::parse($dateRange[1]);

        $articles = Article::withChangelogSumInDateRange($start, $end, ArticleQuantityChangelog::TYPE_OUTGOING, 'usage')
            ->with(['unit'])
            ->whereNotNull('packaging_category')
            ->get()
            ->groupBy(function ($article) {
                return $article->packaging_category;
            });

        return view('reports.article_weight_report', compact('articles', 'dateRange'));
    }
}
