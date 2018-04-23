<?php

namespace Mss\DataTables;

use Mss\Models\Article;

class ArticleDataTable extends BaseDataTable
{
    /**
     * @var array
     */
    protected $rawColumns = ['action', 'price', 'checkbox'];

    /**
     * @var bool
     */
    protected $sortingEnabled = true;

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
            ->setRowClass(function ($article) {
                return ($article->quantity <= $article->min_quantity) ? 'bg-danger' : '';
            })
            ->editColumn('min_quantity', function (Article $article) {
                return $article->min_quantity;
            })
            ->editColumn('name', function (Article $article) {
                return link_to_route('article.show', $article->name, ['article' => $article]);
            })
            ->addColumn('price', function (Article $article) {
                return formatPrice(optional($article->currentSupplierArticle)->price / 100);
            })
            ->addColumn('order_number', function (Article $article) {
                return optional($article->currentSupplierArticle)->order_number;
            })
            ->addColumn('delivery_time', function (Article $article) {
                return optional($article->currentSupplierArticle)->delivery_time;
            })
            ->addColumn('supplier', function (Article $article) {
                return optional($article->currentSupplier)->name;
            })
            ->addColumn('order_quantity', function (Article $article) {
                return optional($article->currentSupplierArticle)->order_quantity;
            })
            ->editColumn('category', function (Article $article) {
                return optional($article->category)->name;
            })
            ->addColumn('unit', function (Article $article) {
                return optional($article->unit)->name;
            })
            ->addColumn('tags', function (Article $article) {
                return $article->tags->pluck('name')->implode(', ');
            })
            ->filterColumn('category', function ($query, $keyword) {
                $query->where('category_id', $keyword);
            })
            ->filterColumn('delivery_time', function ($query, $keyword) {
                $query->whereHas('suppliers', function ($query) use ($keyword) {
                    $query->where('delivery_time', $keyword);
                });
            })
            ->filterColumn('supplier', function ($query, $keyword) {
                $query->whereHas('suppliers', function ($query) use ($keyword) {
                    $query->where('suppliers.id', $keyword);
                });
            })
            ->filterColumn('tags', function ($query, $keyword) {
                $query->whereHas('tags', function ($query) use ($keyword) {
                    $query->where('tags.id', $keyword);
                });
            })
            ->filter(function ($query) {
                if (!isset(request('columns')[14]['search'])) {
                    $query->where('status', Article::STATUS_ACTIVE);
                }
            })
            ->orderColumn('supplier', 'supplier_name $1')

            /*->order(function ($query) {
                dd(request()->all());
                if (request()->has('supplier')) {
                    dd(request('supplier'));
                }
            })*/
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
            ->withCurrentSupplierArticle()->withCurrentSupplier()->withCurrentSupplierName()
            ->with(['category', 'suppliers', 'unit', 'tags']);
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
            'order' => [[1, 'asc']]
        ];

        if ($this->sortingEnabled) {
            $parameters['rowReorder'] = [
                'selector' => 'tr>td:nth-child(2)', // I allow all columns for dragdrop except the last
                'dataSrc' => 'sort_id',
                'update' => false // this is key to prevent DT auto update
            ];
        }

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
            ['data' => 'article_number', 'name' => 'article_number', 'title' => '#'],
            ['data' => 'sort_id', 'name' => 'sort_id', 'title' => 'Sortierung', 'visible' => false],
            ['data' => 'name', 'name' => 'name', 'title' => 'Artikelbezeichnung'],
            ['data' => 'order_number', 'name' => 'order_number', 'title' => 'Bestellnummer'],
            ['data' => 'quantity', 'name' => 'quantity', 'title' => 'Bestand', 'class' => 'text-center', 'width' => '40px'],
            ['data' => 'min_quantity', 'name' => 'min_quantity', 'title' => 'M.Bestand', 'class' => 'text-center', 'width' => '55px'],
            ['data' => 'order_quantity', 'name' => 'order_quantity', 'title' => 'B.Menge', 'class' => 'text-center', 'width' => '55px'],
            ['data' => 'unit', 'name' => 'unit', 'title' => 'Einheit'],
            ['data' => 'price', 'name' => 'price', 'title' => 'Preis', 'class' => 'text-right'],
            ['data' => 'notes', 'name' => 'notes', 'title' => 'Bemerkung', 'visible' => false],
            ['data' => 'delivery_time', 'name' => 'delivery_time', 'title' => 'Lieferzeit', 'class' => 'text-right'],
            ['data' => 'supplier', 'name' => 'supplier', 'title' => 'Lieferant'],
            ['data' => 'tags', 'name' => 'tags', 'title' => 'Tags', 'visible' => false],
            ['data' => 'category', 'name' => 'category', 'title' => 'Kategorie', 'visible' => false],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status', 'visible' => false],
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
