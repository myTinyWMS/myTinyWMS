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

class InventoryReport implements FromCollection, WithColumnFormatting, WithEvents, WithStrictNullComparison {
    /**
     * @var string
     */
    protected $month;

    protected $inventoryType;

    /**
     * InventoryReport constructor.
     * @param $month
     */
    public function __construct($month, $inventoryType) {
        $this->month = $month;
        $this->inventoryType = $inventoryType;
    }

    /**
     * @return array
     */
    public function registerEvents(): array {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                foreach(range('A', 'U') as $col) {
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
            'D' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_NUMBER,
            'I' => NumberFormat::FORMAT_NUMBER,
            'J' => NumberFormat::FORMAT_NUMBER,
            'K' => NumberFormat::FORMAT_NUMBER,
            'L' => NumberFormat::FORMAT_NUMBER,
            'M' => NumberFormat::FORMAT_NUMBER,
            'N' => NumberFormat::FORMAT_TEXT,
            'O' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            'P' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            'Q' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            'R' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            'S' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            'T' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            'U' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
        ];
    }

    public function collection() {
        $start = Carbon::parse($this->month.'-01');
        $end = $start->copy()->endOfMonth();

        $articles = !is_null($this->inventoryType) ? Article::where('inventory', $this->inventoryType) : Article::query();

        $articles = $articles
            ->withCurrentSupplier()
            ->withCurrentSupplierArticle()
            ->withChangelogSumInDateRange($start, $end, ArticleQuantityChangelog::TYPE_INCOMING, 'total_incoming')
            ->withChangelogSumInDateRange($start, $end, ArticleQuantityChangelog::TYPE_OUTGOING, 'total_outgoing')
            ->withChangelogSumInDateRange($start, $end, ArticleQuantityChangelog::TYPE_CORRECTION, 'total_correction')
            ->withChangelogSumInDateRange($start, $end, ArticleQuantityChangelog::TYPE_INVENTORY, 'total_inventory')
            ->with(['unit', 'category', 'supplierArticles.audits', 'supplierArticles.supplier', 'audits'])
            ->orderedByArticleNumber()
            ->get();

        /* @var $articles Collection */
        $articles
            ->transform(function ($article, $key) use ($start, $end) {
                $i = $key + 2;

                /* @var Article $article */
                $currentSupplierArticle = $article->getSupplierArticleAtDate($start);
                $currentPrice = ($currentSupplierArticle) ? $currentSupplierArticle->getAttributeAtDate('price', $start) : 0;
                $status = $article->getAttributeAtDate('status', $start);

                if (!in_array($status, [0,1])) {
                    dd($article);
                }

                if (!$currentSupplierArticle->supplier) {
                    dd($article, $currentSupplierArticle);
                }

                return [
                    'Artikelnummer' => $article->article_number,
                    'Artikelname' => $article->getAttributeAtDate('name', $start),
                    'Lieferant' => optional($currentSupplierArticle->supplier)->name,
                    'Preis' => $currentPrice ? round(($currentPrice / 100), 2) : 0,
                    'Bestellnummer' => optional($currentSupplierArticle)->order_number,
                    'Kategorie' => optional($article->category)->name,
                    'Einheit' => optional($article->unit)->name,
                    'Status' => $status ? Article::getStatusTextArray()[$status] : '',
                    'Anfangsbestand' => $article->getAttributeAtDate('quantity', $start),
                    'Warenausgang' => $article->total_outgoing ?? 0,
                    'Wareneingang' => $article->total_incoming ?? 0,
                    'Korrektur' => $article->total_correction ?? 0,
                    'Inventur' => $article->total_inventory ?? 0,
                    'Endbestand' => $article->getAttributeAtDate('quantity', $end),
                    'Monat' => $this->month,
                    'AB Eur' => "=I$i*\$D$i",
                    'WA Eur' => "=J$i*\$D$i",
                    'WE Eur' => "=K$i*\$D$i",
                    'KO Eur' => "=L$i*\$D$i",
                    'INV Eur' => "=M$i*\$D$i",
                    'EB Eur' => "=N$i*\$D$i",
                    'Kontrolle' => "=-(P$i+Q$i+R$i-U$i)",
                ];
            });

        $articles->prepend(array_keys($articles->first()));
        return $articles;
    }
}