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
            ->editColumn('quantity', function (Article $article) {
                return $article->formatQuantity($article->quantity);
            })
            ->editColumn('min_quantity', function (Article $article) {
                return $article->formatQuantity($article->min_quantity);
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
                return $article->formatQuantity($article->currentSupplierArticle()->order_quantity);
            })
            ->addColumn('category', function (Article $article) {
                return $article->categories()->pluck('name')->implode(', ');
            })
            ->filterColumn('category', function ($query, $keyword) {
                $query->whereHas('categories', function ($query) use ($keyword) {
                    $query->where('categories.id', $keyword);
                });
            })
            ->filterColumn('supplier', function ($query, $keyword) {
                $query->whereHas('suppliers', function ($query) use ($keyword) {
                    $query->where('suppliers.id', $keyword);
                });
            })
            ->addColumn('action', 'articledatatable.action')
            ->rawColumns(['inventory']);
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
            ->with(['categories', 'suppliers', 'unit']);
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
                    'selector' => 'tr>td:not(:last-child)', // I allow all columns for dragdrop except the last
                    'dataSrc' => 'sort_id',
                    'update' => false // this is key to prevent DT auto update
                ]
            ]);

        /*return $this->builder()
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->addAction(['width' => '80px'])
                    ->parameters($this->getBuilderParameters());*/
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            ['data' => 'id', 'name' => 'id', 'title' => '#'],
            ['data' => 'sort_id', 'name' => 'sort_id', 'title' => 'Sortierung', 'visible' => false],
            ['data' => 'name', 'name' => 'name', 'title' => 'Arikelbezeichnung'],
            ['data' => 'order_number', 'name' => 'order_number', 'title' => 'Bestellnummer'],
            ['data' => 'quantity', 'name' => 'quantity', 'title' => 'Bestand'],
            ['data' => 'min_quantity', 'name' => 'min_quantity', 'title' => 'M-Bestand'],
            ['data' => 'order_quantity', 'name' => 'order_quantity', 'title' => 'Bestellmenge'],
            ['data' => 'price', 'name' => 'price', 'title' => 'Preis'],
            ['data' => 'notes', 'name' => 'notes', 'title' => 'Bemerkung'],
            ['data' => 'delivery_time', 'name' => 'delivery_time', 'title' => 'Lieferzeit'],
            ['data' => 'supplier', 'name' => 'supplier', 'title' => 'Lieferant'],
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
