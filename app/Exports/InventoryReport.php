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
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_NUMBER,
            'K' => NumberFormat::FORMAT_NUMBER,
            'L' => NumberFormat::FORMAT_NUMBER,
            'M' => NumberFormat::FORMAT_NUMBER,
            'N' => NumberFormat::FORMAT_NUMBER,
            'O' => NumberFormat::FORMAT_NUMBER,
            'P' => NumberFormat::FORMAT_NUMBER,
            'Q' => NumberFormat::FORMAT_NUMBER,
            'R' => NumberFormat::FORMAT_TEXT,
            'S' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            'T' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            'U' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            'V' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            'W' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            'X' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            'Y' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            'Z' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
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
            ->withChangelogSumInDateRange($start, $end, ArticleQuantityChangelog::TYPE_TRANSFER, 'total_transfer')
            ->withChangelogSumInDateRange($start, $end, ArticleQuantityChangelog::TYPE_SALE_TO_THIRD_PARTIES, 'total_sale_to_third_parties')
            ->with(['unit', 'category', 'supplierArticles.audits', 'supplierArticles.supplier', 'supplierArticles.article', 'audits'])
            ->orderedByArticleNumber()
            ->get();

        $articles = $articles->filter(function ($article) use ($end) {
            $ignoreArticleCreatedDate = (!empty(env('LAST_ARTICLE_ID_CREATED_ON_FIRST_IMPORT')) && $article->id <= env('LAST_ARTICLE_ID_CREATED_ON_FIRST_IMPORT'));
            return ($ignoreArticleCreatedDate || $article->created_at->lt($end));
        });

        // reset keys
        $articles = collect($articles->values());

        /* @var $articles Collection */
        $articles
            ->transform(function ($article, $key) use ($start, $end) {
                $i = $key + 2;

                /* @var Article $article */
                $currentSupplierArticle = $article->getSupplierArticleAtDate($end);
                $currentPrice = ($currentSupplierArticle) ? $currentSupplierArticle->getAttributeAtDate('price', $end) : 0;
                $status = $article->getAttributeAtDate('status', $end);

                if (!$currentSupplierArticle) {
                    return [];
                }

                return [
                    __('Interne Artikelnummer') => $article->internal_article_number,
                    __('Artikelname') => $article->getAttributeAtDate('name', $end),
                    __('Lieferant') => $currentSupplierArticle->supplier ? $currentSupplierArticle->supplier->name : '',
                    __('Kreditorennummer') => $currentSupplierArticle->supplier ? $currentSupplierArticle->supplier->accounts_payable_number : '',
                    __('Preis') => $currentPrice ? round(($currentPrice / 100), 2) : 0,
                    __('Bestellnummer') => optional($currentSupplierArticle)->order_number,
                    __('Kostenstelle') => optional($currentSupplierArticle)->cost_center,
                    __('Kategorie') => optional($article->category)->name,
                    __('Einheit') => optional($article->unit)->name,
                    __('Status') => in_array($status, array_keys(Article::getStatusTextArray())) ? Article::getStatusTextArray()[$status] : '',
                    __('Anfangsbestand') => $article->getAttributeAtDate('quantity', $start->copy()->subDay()),   // getQuantityAtDate uses end of the day
                    __('Warenausgang') => $article->total_outgoing ?? 0,
                    __('Wareneingang') => $article->total_incoming ?? 0,
                    __('Korrektur') => $article->total_correction ?? 0,
                    __('Verkauf an Fremdfirmen') => $article->total_sale_to_third_parties ?? 0,
                    __('Inventur') => $article->total_inventory ?? 0,
                    __('Umbuchung') => $article->total_transfer ?? 0,
                    __('Endbestand') => $article->getAttributeAtDate('quantity', $end),
                    __('Monat') => $this->month,
                    __('AB Eur') => "=K$i*\$E$i",
                    __('WA Eur') => "=L$i*\$E$i",
                    __('WE Eur') => "=M$i*\$E$i",
                    __('KO Eur') => "=N$i*\$E$i",
                    __('VF Eur') => "=O$i*\$E$i",
                    __('INV Eur') => "=P$i*\$E$i",
                    __('UB Eur') => "=Q$i*\$E$i",
                    __('EB Eur') => "=R$i*\$E$i",
                    __('Kontrolle') => "=-(T$i+U$i+V$i-AA$i)",
                ];
            });

        $articles->prepend(array_keys($articles->first()));
        return $articles;
    }
}