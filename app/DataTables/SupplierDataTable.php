<?php

namespace Mss\DataTables;

use Mss\Models\Supplier;

class SupplierDataTable extends BaseDataTable
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
            ->editColumn('name', function (Supplier $supplier) {
                return link_to_route('supplier.show', $supplier->name, ['supplier' => $supplier]);
            })
            ->addColumn('action', 'supplier.list_action')
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \Mss\Models\Supplier $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Supplier $model)
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
                'order'   => [[0, 'asc']],
            ])
            ->addAction(['title' => 'Aktion', 'width' => '150px']);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
            ['data' => 'email', 'name' => 'email', 'title' => 'E-Mail'],
            ['data' => 'phone', 'name' => 'phone', 'title' => 'Telefon'],
            ['data' => 'contact_person', 'name' => 'contact_person', 'title' => 'Kontaktperson'],
            ['data' => 'website', 'name' => 'website', 'title' => 'Webseite'],
            ['data' => 'notes', 'name' => 'notes', 'title' => 'Bemerkung'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Supplier_' . date('YmdHis');
    }
}
