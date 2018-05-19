<?php

namespace Mss\Services;

use Carbon\Carbon;
use Mss\Models\Article;
use Barryvdh\Snappy\PdfWrapper;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class InventoryService {

    /**
     * @param Carbon $date
     * @return PdfWrapper
     */
    public static function generatePdf(Carbon $date) {
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
        return Excel::create('inventory_'.$date->format('Y-m-d'), function($excel) {
            /* @var LaravelExcelWriter $excel */
            $excel->sheet('sheet1', function($sheet) {
                $articles = Article::where('inventory', true)->withCurrentSupplierArticle()->active()->orderedByArticleNumber()->with(['unit', 'category'])->get();
                $articles
                    ->filter(function ($article) {
                        return (!empty($article->quantity));
                    })
                    ->transform(function ($article) {
                    /* @var Article $article */
                    return [
                        'Kategorie' => $article->category->name,
                        'Artikelname' => $article->name,
                        'Artikelnummer' => $article->article_number,
                        'Bestand' => $article->quantity ?? 0,
                        'Einheit' => optional($article->unit)->name,
                        'aktueller Preis' => round(($article->currentSupplierArticle->price / 100), 2),
                        'Gesamtbetrag' => round((($article->currentSupplierArticle->price * $article->quantity) / 100), 2)
                    ];
                });
                $sheet->fromArray($articles->toArray());
            });

        })->string('xlsx');
    }
}