<?php

namespace Mss\DataTables;

use Mss\Models\Article;

class ArticleDataTable extends BaseDataTable
{
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
                return (empty($article->article_number)) ? '('.$article->quantity.')' : $article->article_number;
            })
            ->editColumn('quantity', function (Article $article) {
                return $article->quantity;
            })
            ->editColumn('min_quantity', function (Article $article) {
                return $article->min_quantity;
            })
            ->editColumn('name', function (Article $article) {
                return link_to_route('article.show', $article->name, ['article' => $article]);
            })
            ->addColumn('price', function (Article $article) {
                return formatPrice($article->currentSupplierArticle()->price);
            })
            ->addColumn('order_number', function (Article $article) {
                return $article->currentSupplierArticle()->order_number;
            })
            ->addColumn('delivery_time', function (Article $article) {
                return $article->currentSupplierArticle()->delivery_time;
            })
            ->addColumn('supplier', function (Article $article) {
                return $article->currentSupplier()->name;
            })
            ->addColumn('order_quantity', function (Article $article) {
                return $article->currentSupplierArticle()->order_quantity;
            })
            ->editColumn('category', function (Article $article) {
                return $article->category->name;
            })
            ->addColumn('unit', function (Article $article) {
                return $article->unit->name;
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
            ->addColumn('action', 'article.list_action')
            ->rawColumns(['action']);
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
            ->parameters([
                'order'   => [[1, 'asc']],
                'rowReorder' => [
                    'selector' => 'tr>td:first-child', // I allow all columns for dragdrop except the last
                    'dataSrc' => 'sort_id',
                    'update' => false // this is key to prevent DT auto update
                ]
            ])
            ->addAction(['title' => '', 'width' => '80px']);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            ['data' => 'article_number', 'name' => 'article_number', 'title' => '#'],
            ['data' => 'sort_id', 'name' => 'sort_id', 'title' => 'Sortierung', 'visible' => false],
            ['data' => 'name', 'name' => 'name', 'title' => 'Artikelbezeichnung'],
            ['data' => 'order_number', 'name' => 'order_number', 'title' => 'Bestellnummer'],
            ['data' => 'quantity', 'name' => 'quantity', 'title' => 'Bestand', 'class' => 'text-right'],
            ['data' => 'min_quantity', 'name' => 'min_quantity', 'title' => 'M-Bestand', 'class' => 'text-right'],
            ['data' => 'order_quantity', 'name' => 'order_quantity', 'title' => 'Bestellmenge', 'class' => 'text-right'],
            ['data' => 'unit', 'name' => 'unit', 'title' => 'Einheit'],
            ['data' => 'price', 'name' => 'price', 'title' => 'Preis', 'class' => 'text-right'],
            ['data' => 'notes', 'name' => 'notes', 'title' => 'Bemerkung'],
            ['data' => 'delivery_time', 'name' => 'delivery_time', 'title' => 'Lieferzeit', 'class' => 'text-right'],
            ['data' => 'supplier', 'name' => 'supplier', 'title' => 'Lieferant'],
            ['data' => 'tags', 'name' => 'tags', 'title' => 'Tags'],
            ['data' => 'category', 'name' => 'category', 'title' => 'Kategorie', 'visible' => false],
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
