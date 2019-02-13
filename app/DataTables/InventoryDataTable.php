<?php

namespace Mss\DataTables;

use Mss\Models\Inventory;
use Mss\Services\InventoryService;

class InventoryDataTable extends BaseDataTable
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
            ->addColumn('started', function (Inventory $inventory) {
                return $inventory->created_at->format('d.m.Y H:i');
            })
            ->addColumn('open_categories', function (Inventory $inventory) {
                return InventoryService::getOpenCategories($inventory)->count();
            })
            ->addColumn('open_articles', function (Inventory $inventory) {
                $inventory->load(['items' => function ($query) {
                    $query->unprocessed()->with('article.category');
                }]);

                return $inventory->items->count();
            })
            ->addColumn('action', 'inventory.list_action')
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \Mss\Models\Inventory $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Inventory $model)
    {
        return $model->newQuery()->unfinished();
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
                'paging' => false,
                'order'   => [[0, 'asc']],
            ])
            ->addAction(['title' => 'Aktion', 'width' => '170px']);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            ['data' => 'started', 'name' => 'started', 'title' => 'Gestartet'],
            ['data' => 'open_categories', 'name' => 'open_categories', 'title' => 'offene Kategorien'],
            ['data' => 'open_articles', 'name' => 'open_articles', 'title' => 'offene Artikel'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Unit_' . date('YmdHis');
    }
}
