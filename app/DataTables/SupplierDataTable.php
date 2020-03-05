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
            ['data' => 'accounts_payable_number', 'name' => 'accounts_payable_number', 'title' => __('Kreditorennummer')],
            ['data' => 'email', 'name' => 'email', 'title' => __('E-Mail')],
            ['data' => 'phone', 'name' => 'phone', 'title' => __('Telefon')],
            ['data' => 'contact_person', 'name' => 'contact_person', 'title' => __('Kontaktperson')],
            ['data' => 'website', 'name' => 'website', 'title' => __('Webseite')],
            ['data' => 'notes', 'name' => 'notes', 'title' => __('Bemerkung')],
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
