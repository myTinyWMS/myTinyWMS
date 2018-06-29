<?php

namespace Mss\Services;

use Carbon\Carbon;
use Mss\Exports\InventoryReport;
use Mss\Exports\MonthlyInventoryList;
use Mss\Models\Article;
use Barryvdh\Snappy\PdfWrapper;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Facades\Excel;
use Mss\Models\ArticleQuantityChangelog;
use Mss\Models\OrderItem;

class InventoryService {

    /**
     * @return PdfWrapper
     */
    public static function generatePdf() {
        $articles = Article::where('inventory', true)->active()->orderedByArticleNumber()->with(['unit', 'category'])->get();
        $groupedArticles = $articles->groupBy(function ($article) {
            return $article->category->name;
        })->ksort();

        $pdf = App::make('snappy.pdf.wrapper');
        return $pdf->loadView('documents.inventory', compact('groupedArticles'))->setPaper('a4')->setOrientation('landscape');
    }

    /**
     * @param Carbon $date
     * @return string
     */
    public static function generateExcel(Carbon $date) {
        $filename = 'inventory_'.$date->format('Y-m-d').'.xlsx';
        Excel::store(new MonthlyInventoryList($date), $filename, 'local');

        return storage_path('app/'.$filename);
    }

    public static function generateReport($month, $inventoryType) {
        return Excel::download(new InventoryReport($month, $inventoryType), 'inventory_report_'.$month.'.xlsx');
    }

    public static function generateReportAsFile($month, $inventoryType) {
        Excel::store(new InventoryReport($month, $inventoryType), 'inventory_report_'.$month.'.xlsx');
        return storage_path('app/inventory_report_'.$month.'.xlsx');
    }

    public static function generateDeliveriesWithoutInvoiceReport($date) {
        $openItems = OrderItem::with(['order', 'article'])->whereHas('order.deliveries')->where('invoice_received', 0)->get()->filter(function ($orderItem) {
            return $orderItem->deliveryItems->sum('quantity');
        });

        $filename = 'deliveries_without_invoice_'.$date->format('Y-m-d').'.pdf';
        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->loadView('documents.delivery_without_invoice', compact('openItems'))->setPaper('a4')->setOrientation('landscape')->save(storage_path('app/'.$filename));

        return storage_path('app/'.$filename);
    }

    public static function generateInvoicesWithoutDeliveryReport($date) {
        $openItems = OrderItem::with(['order', 'article'])->where('invoice_received', 1)->whereDoesntHave('order.deliveries')->get();

        $filename = 'invoices_without_delivery_'.$date->format('Y-m-d').'.pdf';
        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->loadView('documents.invoices_without_delivery', compact('openItems'))->setPaper('a4')->setOrientation('landscape')->save(storage_path('app/'.$filename));

        return storage_path('app/'.$filename);
    }
}