<?php

namespace Mss\Exports;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Events\AfterSheet;

class InventoryReport implements FromQuery, WithMapping, WithHeadings, WithCustomChunkSize, WithColumnFormatting, WithEvents, WithStrictNullComparison {
    /**
     * @var string
     */
    protected $month;

    protected $inventoryType;

    protected $rowNumber = 1;

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
                foreach (array_merge(range('A', 'Z'), range('AA', 'AB')) as $col) {
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

    public function query() {
        $start = Carbon::parse($this->month.'-01');
        $end = $start->copy()->endOfMonth();
        $importedArticleThreshold = env('LAST_ARTICLE_ID_CREATED_ON_FIRST_IMPORT');

        $articles = !is_null($this->inventoryType) ? Article::where('inventory', $this->inventoryType) : Article::query();

        return $articles
            ->withCurrentSupplier()
            ->withCurrentSupplierArticle()
            ->withChangelogSumInDateRange($start, $end, ArticleQuantityChangelog::TYPE_INCOMING, 'total_incoming')
            ->withChangelogSumInDateRange($start, $end, ArticleQuantityChangelog::TYPE_OUTGOING, 'total_outgoing')
            ->withChangelogSumInDateRange($start, $end, ArticleQuantityChangelog::TYPE_CORRECTION, 'total_correction')
            ->withChangelogSumInDateRange($start, $end, ArticleQuantityChangelog::TYPE_INVENTORY, 'total_inventory')
            ->withChangelogSumInDateRange($start, $end, ArticleQuantityChangelog::TYPE_TRANSFER, 'total_transfer')
            ->withChangelogSumInDateRange($start, $end, ArticleQuantityChangelog::TYPE_SALE_TO_THIRD_PARTIES, 'total_sale_to_third_parties')
            ->with(['unit', 'category', 'supplierArticles.supplier'])
            ->orderedByArticleNumber()
            ->when(!empty($importedArticleThreshold), function (Builder $query) use ($end, $importedArticleThreshold) {
                $query->where(function (Builder $query) use ($end, $importedArticleThreshold) {
                    $query->where('id', '<=', $importedArticleThreshold)
                        ->orWhere('created_at', '<', $end);
                });
            }, function (Builder $query) use ($end) {
                $query->where('created_at', '<', $end);
            });
    }

    public function prepareRows($rows) {
        $end = Carbon::parse($this->month.'-01')->endOfMonth();

        return $rows->filter(function (Article $article) use ($end) {
            return !is_null($article->getSupplierArticleAtDate($end));
        })->values();
    }

    public function headings(): array {
        return [
            __('Interne Artikelnummer'),
            __('Artikelname'),
            __('Lieferant'),
            __('Kreditorennummer'),
            __('Preis'),
            __('Bestellnummer'),
            __('Kostenstelle'),
            __('Kategorie'),
            __('Einheit'),
            __('Status'),
            __('Anfangsbestand'),
            __('Warenausgang'),
            __('Wareneingang'),
            __('Korrektur'),
            __('Verkauf an Fremdfirmen'),
            __('Inventur'),
            __('Umbuchung'),
            __('Endbestand'),
            __('Monat'),
            __('AB Eur'),
            __('WA Eur'),
            __('WE Eur'),
            __('KO Eur'),
            __('VF Eur'),
            __('INV Eur'),
            __('UB Eur'),
            __('EB Eur'),
            __('Kontrolle'),
        ];
    }

    public function map($article): array {
        $start = Carbon::parse($this->month.'-01');
        $end = $start->copy()->endOfMonth();
        $currentSupplierArticle = $article->getSupplierArticleAtDate($end);
        $currentPrice = $currentSupplierArticle ? $currentSupplierArticle->getAttributeAtDate('price', $end) : 0;
        $status = $article->getAttributeAtDate('status', $end);
        $excelRow = ++$this->rowNumber;

        return [
            $article->internal_article_number,
            $article->getAttributeAtDate('name', $end),
            $currentSupplierArticle->supplier ? $currentSupplierArticle->supplier->name : '',
            $currentSupplierArticle->supplier ? $currentSupplierArticle->supplier->accounts_payable_number : '',
            $currentPrice ? round(($currentPrice / 100), 2) : 0,
            optional($currentSupplierArticle)->order_number,
            optional($currentSupplierArticle)->cost_center,
            optional($article->category)->name,
            optional($article->unit)->name,
            in_array($status, array_keys(Article::getStatusTextArray())) ? Article::getStatusTextArray()[$status] : '',
            $article->getAttributeAtDate('quantity', $start->copy()->subDay()),
            $article->total_outgoing ?? 0,
            $article->total_incoming ?? 0,
            $article->total_correction ?? 0,
            $article->total_sale_to_third_parties ?? 0,
            $article->total_inventory ?? 0,
            $article->total_transfer ?? 0,
            $article->getAttributeAtDate('quantity', $end),
            $this->month,
            "=K$excelRow*\$E$excelRow",
            "=L$excelRow*\$E$excelRow",
            "=M$excelRow*\$E$excelRow",
            "=N$excelRow*\$E$excelRow",
            "=O$excelRow*\$E$excelRow",
            "=P$excelRow*\$E$excelRow",
            "=Q$excelRow*\$E$excelRow",
            "=R$excelRow*\$E$excelRow",
            "=-(T$excelRow+U$excelRow+V$excelRow-AA$excelRow)",
        ];
    }

    public function chunkSize(): int {
        return 250;
    }
}
