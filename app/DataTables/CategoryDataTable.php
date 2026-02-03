<?php

namespace Mss\DataTables;

use Mss\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryDataTable extends BaseDataTable
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
            ->editColumn('name', function (Category $category) {
                return link_to_route('category.show', $category->name, ['category' => $category]);
            })
            ->addColumn('inventory_value', function (Category $category) {
                return formatPrice($category->getInventoryValue());
            })
            ->addColumn('action', 'category.list_action')
            ->addColumn('checkbox', 'category.list_checkbox')
            ->rawColumns(['action', 'checkbox', 'inventory_value']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \Mss\Models\Category $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Category $model)
    {
        return $model->newQuery();
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
                'order'   => [[1, 'asc']],
                'buttons' => []
            ])
            ->addAction(['title' => __('Aktion'), 'width' => '150px']);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            ['data' => 'checkbox', 'name' => 'checkbox', 'title' => '', 'width' => '10px', 'orderable' => false],
            ['data' => 'name', 'name' => 'name', 'title' => __('Name')],
            ['data' => 'notes', 'name' => 'notes', 'title' => __('Bemerkung')],
            ['data' => 'inventory_value', 'name' => 'inventory_value', 'title' => __('Lagerwert'), 'class' => 'text-right', 'orderable' => false, 'searchable' => false],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Category_' . date('YmdHis');
    }
}
