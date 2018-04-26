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
                $articles = Article::where('inventory', true)->active()->orderedByName()->with(['unit', 'category'])->get();
                $articles->transform(function ($article) {
                    /* @var Article $article */
                    return [
                        'name' => $article->name,
                        'nummer' => $article->article_number,
                        'kategorie' => $article->category->name,
                        'bestand' => $article->quantity,
                        'mindestbestand' => $article->min_quantity,
                        'einheit' => optional($article->unit)->name
                    ];
                });
                $sheet->fromArray($articles->toArray());
            });

        })->string('xlsx');
    }
}