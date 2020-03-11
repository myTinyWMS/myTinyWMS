<?php

namespace Mss\DataTables;

use Mss\Models\User;

class UserDataTable extends BaseDataTable
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
            ->editColumn('name', function (User $user) {
                return link_to_route('user.show', $user->name, compact('user'));
            })
            ->addColumn('source', function (User $user) {
                return ($user->getSource() == User::SOURCE_LDAP) ? 'LDAP' : __('Lokal');
            })
            ->addColumn('roles', function (User $user) {
                return $user->getRoleNames()->implode(', ');
            })
            ->addColumn('action', 'user.list_action')
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \Mss\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
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
            ['data' => 'email', 'name' => 'email', 'title' => __('E-Mail')],
            ['data' => 'username', 'name' => 'username', 'title' => __('Benutzername')],
            ['data' => 'source', 'name' => 'source', 'title' => __('Quelle')],
            ['data' => 'roles', 'name' => 'roles', 'title' => __('Rollen')],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Users_' . date('YmdHis');
    }
}
