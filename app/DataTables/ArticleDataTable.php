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
    protected $rawColumns = ['action', 'price', 'checkbox', 'order_number', 'supplier_name'];

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
            ->setRowId('id')
            ->editColumn('article_number', function (Article $article) {
                return (empty($article->article_number)) ? '('.$article->id.')' : $article->article_number;
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
                    $orderNumber .= '<i class="fa fa-shopping-cart pull-right" title="offene Bestellung"></i>';
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
            ->editColumn('supplier_name', 'article.list_supplier')
            ->addColumn('unit', function (Article $article) {
                return optional($article->unit)->name;
            })
            ->addColumn('tags', function (Article $article) {
                return $article->tags->pluck('name')->implode(', ');
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
                $query->whereRaw('(SELECT supplier_id FROM article_supplier WHERE article_supplier.article_id = articles.id order by created_at desc limit 1) = '.$keyword);
            })
            ->filterColumn('tags', function ($query, $keyword) {
                $query->whereHas('tags', function ($query) use ($keyword) {
                    $query->where('tags.id', $keyword);
                });
            })
            ->filterColumn('status', function ($query, $keyword) {
                if ($keyword == 'all') {
                    $query->whereIn('status', [Article::STATUS_ACTIVE, Article::STATUS_INACTIVE]);
                } else {
                    $query->where('status', $keyword);
                }
            })
            ->filter(function ($query) {
                if (!isset(request('columns')[15]['search']) && !isset(request('columns')[17]['search'])) {
                    $query->where('status', Article::STATUS_ACTIVE);
                }
            })
            ->orderColumn('supplier', 'supplier_name $1')
            ->addColumn('action', 'article.list_action')
            ->addColumn('checkbox', 'article.list_checkbox')
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
            ->withCurrentSupplierArticle()->withCurrentSupplier()->withCurrentSupplierName()->withAverageUsage()->withLastReceipt()
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
            ->addAction(['title' => '', 'width' => '80px']);
    }

    protected function getHtmlParameters() {
        $parameters = [
            'order' => [[2, 'asc']]
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
            ['data' => 'checkbox', 'name' => 'checkbox', 'title' => '<input type="checkbox" value="" id="select_all" />', 'width' => '10px', 'orderable' => false, 'class' => 'text-center'],
            ['data' => 'sort_id', 'name' => 'sort_id', 'title' => 'Sort.', 'width' => '40px', 'visible' => false],
            ['data' => 'article_number', 'name' => 'article_number', 'title' => '#'],
            ['data' => 'name', 'name' => 'name', 'title' => 'Artikelbezeichnung'],
            ['data' => 'order_number', 'name' => 'order_number', 'title' => 'Bestellnummer'],
            ['data' => 'quantity', 'name' => 'quantity', 'title' => 'Bestand', 'class' => 'text-center', 'width' => '40px'],
            ['data' => 'min_quantity', 'name' => 'min_quantity', 'title' => 'M.Bestand', 'class' => 'text-center', 'width' => '55px'],
            ['data' => 'order_quantity', 'name' => 'order_quantity', 'title' => 'B.Menge', 'class' => 'text-center', 'width' => '55px'],
            ['data' => 'average_usage', 'name' => 'average_usage', 'title' => '&#x00D8; Verbr.', 'class' => 'text-center', 'width' => '60px'],
            ['data' => 'unit', 'name' => 'unit', 'title' => 'Einheit'],
            ['data' => 'price', 'name' => 'price', 'title' => 'Preis', 'class' => 'text-right'],
            ['data' => 'notes', 'name' => 'notes', 'title' => 'Bemerkung', 'visible' => false],
            ['data' => 'delivery_time', 'name' => 'delivery_time', 'title' => 'Lieferzeit', 'class' => 'text-right'],
            ['data' => 'supplier_name', 'name' => 'supplier_name', 'title' => 'Lieferant'],
            ['data' => 'last_receipt', 'name' => 'last_receipt', 'title' => 'letzter WE', 'width' => '70px'],
            ['data' => 'tags', 'name' => 'tags', 'title' => 'Tags', 'visible' => false],
            ['data' => 'category', 'name' => 'category', 'title' => 'Kategorie', 'visible' => false],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status', 'visible' => false],
            ['data' => 'id', 'name' => 'id', 'title' => 'ID', 'visible' => false],
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
