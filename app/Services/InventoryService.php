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

    public static function generateReport($month) {
        return Excel::download(new InventoryReport($month), 'inventory_report_'.$month.'.xlsx');
    }
}