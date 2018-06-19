<?php

namespace Mss\Exports;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Events\AfterSheet;

class MonthlyInventoryList implements FromCollection, WithColumnFormatting, WithEvents, WithStrictNullComparison {
    /**
     * @var Carbon
     */
    protected $date;

    /**
     * InventoryReport constructor.
     * @param Carbon $date
     */
    public function __construct($date) {
        $this->date = $date;
    }

    /**
     * @return array
     */
    public function registerEvents(): array {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                foreach(range('A', 'G') as $col) {
                    $event->getSheet()->getDelegate()->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            'G' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
        ];
    }

    public function collection() {
        /* @var $articles Collection */
        $articles = Article::where('inventory', true)
            ->withCurrentSupplierArticle()
            ->orderedByArticleNumber()
            ->withQuantityAtDate($this->date, 'current_quantity')
            ->with(['unit', 'category'])
            ->get();

        // filter empty items
        $articles = $articles->filter(function ($article) {
            return ($article->getQuantityAtDate($this->date, 'current_quantity') > 0);
        });

        // reset keys
        $articles = collect($articles->values());
        $articles->transform(function ($article, $key) {
                $quantity = $article->getQuantityAtDate($this->date, 'current_quantity');
                $i = $key + 2;
                /* @var Article $article */
                return [
                    'Kategorie' => $article->category->name,
                    'Artikelname' => $article->name,
                    'Artikelnummer' => $article->article_number,
                    'Bestand' => $quantity,
                    'Einheit' => optional($article->unit)->name,
                    'aktueller Preis' => round(($article->currentSupplierArticle->price / 100), 2),
                    'Gesamtbetrag' => "=D$i*F$i"
                ];
            });

        $articles->prepend(array_keys($articles->first()));
        return $articles;
    }
}