<?php

namespace Mss\DataTables;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Mss\Models\Article;
use Mss\Models\Order;
use Mss\Models\OrderItem;

class ToOrderDataTable extends ArticleDataTable
{
    /**
     * @var bool
     */
    protected $sortingEnabled = false;

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = parent::dataTable($query);
        $dataTable->setRowClass(function ($article) {
            return '';
        })
        ->setRowAttr([
            'data-supplier' => function ($article) {
                return $article->currentSupplier->id;
            }
        ])
        ->editColumn('checkbox', 'dashboard.to_order_list_checkbox');

        return $dataTable;
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
            ->with(['category', 'suppliers', 'unit', 'tags'])
            ->whereRaw('quantity <= min_quantity')
            ->where('min_quantity', '>', -1)
            ->whereDoesntHave('orderItems', function ($query) {
                $query->whereHas('order', function ($query) {
                    $query->statusOpen();
                });
            });
    }
}
