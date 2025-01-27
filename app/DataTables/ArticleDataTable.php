<?php

namespace Mss\DataTables;

use Carbon\Carbon;
use Mss\Models\Article;

class ArticleDataTable extends BaseDataTable
{
    const STATUS_COL_ID = 17;
    const CATEGORY_COL_ID = 16;
    const TAGS_COL_ID = 15;
    const SUPPLIER_COL_ID = 13;

    /**
     * @var array
     */
    protected $rawColumns = ['action', 'price', 'checkbox', 'order_number', 'supplier_name', 'average_usage'];

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
            ->setRowId('article_{{$id}}')
            ->editColumn('internal_article_number', function (Article $article) {
                return (empty($article->internal_article_number)) ? '('.$article->id.')' : $article->internal_article_number;
            })
            ->editColumn('quantity', function (Article $article) {
                return $article->quantity;
            })
            ->editColumn('min_quantity', function (Article $article) {
                return $article->min_quantity;
            })
            ->editColumn('name', function (Article $article) {
                return link_to_route('article.show', $article->name, ['article' => $article], ['target' => '_blank']);
            })
            ->addColumn('price', function (Article $article) {
                return formatPrice(optional($article->currentSupplierArticle)->price / 100);
            })
            ->addColumn('order_number', function (Article $article) {
                $orderNumber = optional($article->currentSupplierArticle)->order_number;

                if ($article->openOrders()->count()) {
                    $orderNumber .= '<i class="fa fa-shopping-cart float-right" title="'.__('offene Bestellung').'"></i>';
                }

                return $orderNumber;
            })
            ->addColumn('average_usage', function (Article $article) {
                return intval($article->average_usage);
            })
            ->addColumn('last_receipt', function (Article $article) {
                $latestReceipt = $article->last_receipt;

                return ($latestReceipt) ? Carbon::parse($latestReceipt)->format('d.m.Y') : '';
            })
            ->addColumn('delivery_time', function (Article $article) {
                return optional($article->currentSupplierArticle)->delivery_time;
            })
            ->addColumn('order_quantity', function (Article $article) {
                return optional($article->currentSupplierArticle)->order_quantity;
            })
            ->editColumn('category', function (Article $article) {
                return optional($article->category)->name;
            })
            ->editColumn('supplier_name', function (Article $article) {
                return '<div class="flex">
                            <div>'.$article->supplier_name.'</div>
                            <div class="flex-1 text-right pr-4">
                                <a href="'.route('article.index', ['supplier' => $article->current_supplier_id]).'"><i class="fa fa-filter"></i></a>
                            </div>
                        </div>';
            })
            ->addColumn('unit', function (Article $article) {
                return optional($article->unit)->name;
            })
            ->addColumn('tags', function (Article $article) {
                return $article->tags->pluck('name')->implode(', ');
            })
            ->addColumn('average_usage', function (Article $article) {
                if ($article->average_usage_12 == 0) return $article->average_usage_12;

                $diff = $article->average_usage_12 - $article->average_usage_3;
                $diffPercent = (100 * $diff) / $article->average_usage_12;

                if ($diffPercent < -30) {
                    return $article->average_usage_12.' <i class="fa fa-angle-double-up text-danger bold" style="font-size: 18px; margin-left: 5px" title="'.__('Verbrauch in den letzten 3 Monaten mind. 30% höher als in den letzten 12').'"></i>';
                } elseif ($diffPercent < -15) {
                    return $article->average_usage_12.' <i class="fa fa-angle-up text-danger bold" style="font-size: 18px; margin-left: 5px" title="'.__('Verbrauch in den letzten 3 Monaten mind. 15% höher als in den letzten 12').'"></i>';
                } elseif ($diffPercent > 30) {
                    return $article->average_usage_12.' <i class="fa fa-angle-double-down text-success bold" style="font-size: 18px; margin-left: 5px" title="'.__('Verbrauch in den letzten 3 Monaten mind. 30% niedriger als in den letzten 12').'"></i>';
                } elseif ($diffPercent > 15) {
                    return $article->average_usage_12.' <i class="fa fa-angle-down text-success bold" style="font-size: 18px; margin-left: 5px" title="'.__('Verbrauch in den letzten 3 Monaten mind. 30% niedriger als in den letzten 12').'"></i>';
                }

                return $article->average_usage_12;
            })
            ->filterColumn('id', function ($query, $keyword) {
                $query->whereIn('id', explode(',', $keyword));
            })
            ->filterColumn('category', function ($query, $keyword) {
                $query->where('category_id', $keyword);
            })
            ->filterColumn('delivery_time', function ($query, $keyword) {
                $query->whereHas('suppliers', function ($query) use ($keyword) {
                    $query->where('delivery_time', $keyword);
                });
            })
            ->filterColumn('supplier_name', function ($query, $keyword) {
                /*
                 * @todo optimize me!
                 */
                $query->whereRaw('(SELECT supplier_id FROM article_supplier WHERE article_supplier.article_id = articles.id order by created_at desc limit 1) = ?', $keyword);
            })
            ->filterColumn('tags', function ($query, $keyword) {
                $query->whereHas('tags', function ($query) use ($keyword) {
                    $query->where('tags.id', $keyword);
                });
            })
            ->filterColumn('status', function ($query, $keyword) {
                if ($keyword == 'all') {
                    $query->whereIn('status', [Article::STATUS_ACTIVE, Article::STATUS_INACTIVE, Article::STATUS_NO_ORDERS]);
                } else {
                    $query->where('status', $keyword);
                }
            })
            ->filter(function ($query) {
                if (!isset(request('columns')[15]['search']) && !isset(request('columns')[17]['search'])) {
                    $query->where('status', Article::STATUS_ACTIVE);
                }
            }, true)
            ->orderColumn('supplier', 'supplier_name $1')
            ->orderColumn('last_receipt', 'last_receipt $1')
            ->addColumn('action', function ($article) {
                return '<a href="'.route('article.show', $article).'" class="table-action" target="_blank">'.__('Details').'</a>';
            })
            ->addColumn('checkbox', function ($article) {
                return '<div class="i-checks"><label><input type="checkbox" value="'.$article->id.'" name="article[]" /></label></div>';
            })
            ->rawColumns($this->rawColumns);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \Mss\Models\Article $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Article $model)
    {
        return $model->newQuery()
            ->withCurrentSupplierArticle()->withCurrentSupplier()->withCurrentSupplierName()->withAverageUsage(12)->withAverageUsage(3)->withLastReceipt()
            ->with(['category', 'suppliers', 'unit', 'tags', 'openOrderItems']);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->minifiedAjax()
            ->columns($this->getColumns())
            ->parameters($this->getHtmlParameters())
            ->addAction(['title' => __('Aktion'), 'width' => '80px', 'class' => 'text-right']);
    }

    protected function getHtmlParameters() {
        $parameters = [
            'order' => [[2, 'asc']],
            'buttons' => [
                ['extend' => 'csv', 'className' => 'btn-secondary', 'text' => '<i class="fa fa-download"></i>', 'titleAttr' => __('Export CSV')]
            ],
        ];

        return $parameters;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            ['data' => 'checkbox', 'name' => 'checkbox', 'title' => '<div class="i-checks"><label><input type="checkbox" value="" id="select_all" /></label></div>', 'width' => '10px', 'orderable' => false, 'class' => 'text-center', 'searchable' => false],
            ['data' => 'sort_id', 'name' => 'sort_id', 'title' => 'Sort.', 'width' => '40px', 'visible' => false, 'searchable' => false],
            ['data' => 'internal_article_number', 'name' => 'internal_article_number', 'title' => '#'],
            ['data' => 'name', 'name' => 'name', 'title' => __('Artikelbezeichnung')],
            ['data' => 'order_number', 'name' => 'order_number', 'title' => __('Bestellnummer')],
            ['data' => 'quantity', 'name' => 'quantity', 'title' => __('Bestand'), 'class' => 'text-center', 'width' => '40px', 'searchable' => false],
            ['data' => 'min_quantity', 'name' => 'min_quantity', 'title' => __('M.Bestand'), 'class' => 'text-center', 'width' => '55px', 'searchable' => false],
            ['data' => 'order_quantity', 'name' => 'order_quantity', 'title' => __('B.Menge'), 'class' => 'text-center', 'width' => '55px', 'searchable' => false],
            ['data' => 'average_usage', 'name' => 'average_usage', 'title' => __('&#x00D8; Verbr.'), 'class' => 'text-center', 'width' => '60px', 'searchable' => false],
            ['data' => 'unit', 'name' => 'unit', 'title' => __('Einheit'), 'searchable' => false],
            ['data' => 'price', 'name' => 'price', 'title' => __('Preis'), 'class' => 'text-right whitespace-no-wrap', 'searchable' => false],
            ['data' => 'notes', 'name' => 'notes', 'title' => __('Bemerkung'), 'visible' => false],
            ['data' => 'delivery_time', 'name' => 'delivery_time', 'title' => __('Lieferzeit'), 'class' => 'text-center', 'searchable' => false],
            ['data' => 'supplier_name', 'name' => 'supplier_name', 'title' => __('Lieferant')],
            ['data' => 'last_receipt', 'name' => 'last_receipt', 'title' => __('letzter WE'), 'width' => '70px', 'class' => 'whitespace-no-wrap', 'searchable' => false],
            ['data' => 'tags', 'name' => 'tags', 'title' => __('Tags'), 'visible' => false],
            ['data' => 'category', 'name' => 'category', 'title' => __('Kategorie'), 'visible' => false],
            ['data' => 'status', 'name' => 'status', 'title' => __('Status'), 'visible' => false],
            ['data' => 'id', 'name' => 'id', 'title' => __('ID'), 'visible' => false],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Article_' . date('YmdHis');
    }
}
