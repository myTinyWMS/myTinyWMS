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

class ArticleUsageReport implements FromCollection, WithColumnFormatting, WithEvents, WithStrictNullComparison {
    /**
     * @var string
     */
    protected $month;

    /**
     * InventoryReport constructor.
     * @param $month
     */
    public function __construct($month) {
        $this->month = $month;
    }

    /**
     * @return array
     */
    public function registerEvents(): array {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                foreach(range('A', 'L') as $col) {
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
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_NUMBER,
            'J' => NumberFormat::FORMAT_NUMBER,
            'K' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            'L' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
        ];
    }

    public function collection() {
        $start1 = Carbon::parse($this->month.'-01');
        $end1 = $start1->copy()->endOfMonth();

        $start2 = $start1->copy()->subYear(1);
        $end2 = $end1->copy()->subYear(1);

        $month1 = $start1->formatLocalized('%b %Y');
        $month2 = $start2->formatLocalized('%b %Y');

        $articles = Article::query();

        $articles = $articles
            ->withCurrentSupplier()
            ->withCurrentSupplierArticle()
            ->withChangelogSumInDateRange($start1, $end1, ArticleQuantityChangelog::TYPE_OUTGOING, 'total_outgoing_month1')
            ->withChangelogSumInDateRange($start2, $end2, ArticleQuantityChangelog::TYPE_OUTGOING, 'total_outgoing_month2')
            ->with(['unit', 'category', 'supplierArticles.audits', 'supplierArticles.supplier', 'supplierArticles.article', 'audits'])
            ->orderedByArticleNumber()
            ->get();

        /*$articles = $articles->filter(function ($article) use ($end) {
            $ignoreArticleCreatedDate = (!empty(env('LAST_ARTICLE_ID_CREATED_ON_FIRST_IMPORT')) && $article->id <= env('LAST_ARTICLE_ID_CREATED_ON_FIRST_IMPORT'));
            return ($ignoreArticleCreatedDate || $article->created_at->lt($end));
        });*/

        // reset keys
        $articles = collect($articles->values());

        /* @var $articles Collection */
        $articles
            ->transform(function ($article, $key) use ($start1, $end1, $month1, $month2) {
                $i = $key + 2;

                /* @var Article $article */
                $currentSupplierArticle = $article->getSupplierArticleAtDate($end1);
                $currentPrice = ($currentSupplierArticle) ? $currentSupplierArticle->getAttributeAtDate('price', $end1) : 0;
                $status = $article->getAttributeAtDate('status', $end1);

                return [
                    'Artikelnummer' => $article->article_number,
                    'Artikelname' => $article->getAttributeAtDate('name', $end1),
                    'Lieferant' => $currentSupplierArticle->supplier ? $currentSupplierArticle->supplier->name : '',
                    'Preis' => $currentPrice ? round(($currentPrice / 100), 2) : 0,
                    'Bestellnummer' => optional($currentSupplierArticle)->order_number,
                    'Kategorie' => optional($article->category)->name,
                    'Einheit' => optional($article->unit)->name,
                    'Status' => in_array($status, array_keys(Article::getStatusTextArray())) ? Article::getStatusTextArray()[$status] : '',
                    'Warenausgang '.$month1 => $article->total_outgoing_month1 ?? 0,
                    'Warenausgang '.$month2 => $article->total_outgoing_month2 ?? 0,
                    'WA Eur '.$month1 => "=I$i*\$D$i",
                    'WA Eur'.$month2 => "=J$i*\$D$i",
                ];
            });

        $articles->prepend(array_keys($articles->first()));
        return $articles;
    }
}