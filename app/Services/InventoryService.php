<?php

namespace Mss\Services;

use Carbon\Carbon;
use Mss\Models\Article;
use Barryvdh\Snappy\PdfWrapper;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;
use Mss\Models\ArticleQuantityChangelog;

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

    public static function generateReport($month) {
        $start = Carbon::parse($month.'-01');
        $end = $start->copy()->lastOfMonth();
        return Excel::create('inventory_report_'.$start->format('Y-m-d').'_'.$end->format('Y-m-d'), function($excel) use ($start, $end, $month) {
            /* @var LaravelExcelWriter $excel */
            $i = 1;
            $excel->sheet('sheet1', function($sheet) use ($start, $end, $month, $i) {
                $articles = Article::where('inventory', true)
                    ->withCurrentSupplier()
                    ->withCurrentSupplierArticle()
                    ->active()
                    ->orderedByArticleNumber()
                    ->withQuantityAtDate($start, 'quantity_start')
                    ->withQuantityAtDate($end, 'quantity_end')
                    ->withChangelogSumInDateRange($start, $end, ArticleQuantityChangelog::TYPE_INCOMING, 'total_incoming')
                    ->withChangelogSumInDateRange($start, $end, ArticleQuantityChangelog::TYPE_OUTGOING, 'total_outgoing')
                    ->withChangelogSumInDateRange($start, $end, ArticleQuantityChangelog::TYPE_CORRECTION, 'total_correction')
                    ->withChangelogSumInDateRange($start, $end, ArticleQuantityChangelog::TYPE_INVENTORY, 'total_inventory')
                    ->with(['unit', 'category'])
                    ->get();
                $articles
                    ->transform(function ($article, $key) use ($month) {
                        $i = $key + 2;
                        /* @var Article $article */
                        return [
                            'Artikelnummer' => $article->article_number,
                            'Artikelname' => $article->name,
                            'Lieferant' => $article->currentSupplier->name,
                            'Preis' => round(($article->currentSupplierArticle->price / 100), 2),
                            'Bestellnummer' => $article->currentSupplierArticle->order_number,
                            'Kategorie' => $article->category->name,
                            'Einheit' => optional($article->unit)->name,
                            'Anfangsbestand' => $article->quantity_start ?? 0,
                            'Warenausgang' => $article->total_outgoing ?? 0,
                            'Wareneingang' => $article->total_incoming ?? 0,
                            'Korrektur' => $article->total_correction ?? 0,
                            'Inventur' => $article->total_inventory ?? 0,
                            'Endestand' => $article->quantity_end ?? 0,
                            'Monat' => $month,
                            'AB Eur' => "=H$i*\$D$i",
                            'WA Eur' => "=I$i*\$D$i",
                            'WE Eur' => "=J$i*\$D$i",
                            'KO Eur' => "=K$i*\$D$i",
                            'INV Eur' => "=L$i*\$D$i",
                            'EB Eur' => "=M$i*\$D$i",
                            'Kontrolle' => "=O$i-P$i+Q$i-T$i",
                        ];
                    });
                $sheet->fromArray($articles->toArray());
            });

        })->string('xlsx');
    }
}