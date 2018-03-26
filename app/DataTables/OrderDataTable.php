<?php

namespace Mss\DataTables;

use Mss\Models\Order;

class OrderDataTable extends BaseDataTable
{
    /**
     * @var string
     */
    protected $actionView = 'order.list_action';

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
            ->addColumn('supplier', function (Order $order) {
                return $order->supplier ? $order->supplier->name : '';
            })
            ->editColumn('order_date', function (Order $order) {
                return !empty($order->order_date) ? $order->order_date->diffForHumans().' <small class="text-muted">('.$order->order_date->format('d.m.Y').')</small>' : '';
            })
            ->editColumn('expected_delivery', function (Order $order) {
                return !empty($order->expected_delivery) ? $order->expected_delivery->diffForHumans().' <small class="text-muted">('.$order->expected_delivery->format('d.m.Y').')</small>' : '';
            })
            ->editColumn('total_cost', function (Order $order) {
                return formatPrice($order->total_cost);
            })
            ->editColumn('internal_order_number', function (Order $order) {
                return view('order.list_order_number', compact('order'))->render();
            })
            ->addColumn('article', function (Order $order) {
                return $order->items->count();
            })
            ->filterColumn('status', function ($query, $keyword) {
                if ($keyword === 'open') {
                    $query->statusOpen();
                } elseif (is_numeric($keyword)) {
                    $query->where('status', $keyword);
                }
            })
            ->filterColumn('supplier', function ($query, $keyword) {
                $query->whereHas('supplier', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->editColumn('status', 'order.status')
            ->addColumn('action', $this->actionView)
            ->rawColumns(['action', 'status', 'order_date', 'expected_delivery', 'total_cost', 'internal_order_number']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \Mss\Models\Order $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Order $model)
    {
        return $model->newQuery()
            ->with(['items', 'items.article', 'supplier', 'messages']);
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
                'order'   => [[0, 'desc']],
            ])
            ->addAction(['title' => '', 'width' => '150px']);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            ['data' => 'internal_order_number', 'name' => 'internal_order_number', 'title' => 'Bestellnummer'],
            ['data' => 'supplier', 'name' => 'supplier', 'title' => 'Lieferant'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
            ['data' => 'article', 'name' => 'article', 'title' => 'Artikel', 'class' => 'text-right'],
            ['data' => 'total_cost', 'name' => 'total_cost', 'title' => 'Gesamtkosten', 'class' => 'text-right', 'searchable' => false],
            ['data' => 'order_date', 'name' => 'order_date', 'title' => 'Bestelldatum', 'class' => 'text-right', 'searchable' => false],
            ['data' => 'expected_delivery', 'name' => 'expected_delivery', 'title' => 'gepl. Lieferdatum', 'class' => 'text-right', 'searchable' => false],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Orders_' . date('YmdHis');
    }
}
