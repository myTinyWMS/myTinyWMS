<?php

namespace Mss\DataTables;

use Spatie\Permission\Models\Role;

class RoleDataTable extends BaseDataTable
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
            ->editColumn('name', function (Role $role) {
                return link_to_route('role.show', $role->name, compact('role'));
            })
            ->addColumn('permissions', function (Role $role) {
                return $role->permissions->count();
            })
            ->addColumn('action', 'role.list_action')
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \Mss\Models\Role $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Role $model)
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
            ['data' => 'name', 'name' => 'name', 'title' => __('Name')],
            ['data' => 'permissions', 'name' => 'permissions', 'title' => __('Berechtigungen')],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Roles_' . date('YmdHis');
    }
}
