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
            ->orderColumn('supplier', 'supplier_name $1')
            ->editColumn('order_date', function (Order $order) {
                return !empty($order->order_date) ? $order->order_date->diffForHumans().' <small class="text-muted">('.$order->order_date->format('d.m.Y').')</small>' : '';
            })
            ->editColumn('expected_delivery', function (Order $order) {
                $expectedDelivery = $order->items->max('expected_delivery');
                return !empty($expectedDelivery) ? $expectedDelivery->diffForHumans().' <small class="text-muted">('.$expectedDelivery->format('d.m.Y').')</small>' : '';
            })
            ->editColumn('internal_order_number', function (Order $order) {
                return view('order.list_order_number', compact('order'))->render();
            })
            ->addColumn('article', function (Order $order) {
                return $order->items->count();
            })
            ->addColumn('invoice_status', 'order.list_invoice_received')
            ->filterColumn('invoice_status', function ($query, $keyword) {
                switch ($keyword) {
                    case 'empty':
                        break;

                    case 'none':
                        $query->whereRaw('(SELECT COUNT(*) FROM order_items WHERE order_id = orders.id AND invoice_received = 1) = 0');
                        break;

                    case 'all':
                        $query->whereRaw('(SELECT COUNT(*) FROM order_items WHERE order_id = orders.id AND invoice_received = 1) = (SELECT COUNT(*) FROM order_items WHERE order_id = orders.id)');
                        break;

                    case 'partial':
                        $query->whereRaw('(SELECT COUNT(*) FROM order_items WHERE order_id = orders.id AND invoice_received = 1) > 0 AND (SELECT COUNT(*) FROM order_items WHERE order_id = orders.id AND invoice_received = 1) < (SELECT COUNT(*) FROM order_items WHERE order_id = orders.id)');
                        break;
                }
            })
            ->addColumn('confirmation_status', 'order.list_confirmation_received')
            ->filterColumn('confirmation_status', function ($query, $keyword) {
                switch ($keyword) {
                    case 'empty':
                        break;

                    case 'none':
                        $query->whereRaw('(SELECT COUNT(*) FROM order_items WHERE order_id = orders.id AND confirmation_received = 1) = 0');
                        break;

                    case 'all':
                        $query->whereRaw('(SELECT COUNT(*) FROM order_items WHERE order_id = orders.id AND confirmation_received = 1) = (SELECT COUNT(*) FROM order_items WHERE order_id = orders.id)');
                        break;

                    case 'partial':
                        $query->whereRaw('(SELECT COUNT(*) FROM order_items WHERE order_id = orders.id AND confirmation_received = 1) > 0 AND (SELECT COUNT(*) FROM order_items WHERE order_id = orders.id AND confirmation_received = 1) < (SELECT COUNT(*) FROM order_items WHERE order_id = orders.id)');
                        break;
                }
            })
            ->filterColumn('status', function ($query, $keyword) {
                if ($keyword === 'open') {
                    $query->statusOpen();
                } elseif (is_numeric($keyword)) {
                    $query->where('status', $keyword);
                }
            })
            ->filterColumn('supplier', function ($query, $keyword) {
                $query->where('supplier_id', $keyword);
            })
            ->filter(function ($query) {
                if (!isset(request('columns')[2]['search'])) {
                    $query->whereIn('status', Order::STATUSES_OPEN);
                }
            })
            ->editColumn('status', 'order.status')
            ->addColumn('action', $this->actionView)
            ->rawColumns(['action', 'status', 'order_date', 'expected_delivery', 'internal_order_number', 'invoice_status', 'confirmation_status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \Mss\Models\Order $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Order $model)
    {
        return $model->newQuery()->withSupplierName()
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
                'paging' => false,
                'order'   => [[0, 'desc']],
                'rowGroup' => ['dataSrc' => 'supplier']
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
            ['data' => 'status', 'name' => 'status', 'title' => 'Bestellstatus', 'width' => '50px', 'class' => 'text-center'],
            ['data' => 'confirmation_status', 'name' => 'confirmation_status', 'title' => 'AB', 'width' => '50px', 'class' => 'text-center', 'orderable' => false],
            ['data' => 'invoice_status', 'name' => 'invoice_status', 'title' => 'Rechnung', 'width' => '50px', 'class' => 'text-center', 'orderable' => false],
            ['data' => 'article', 'name' => 'article', 'title' => 'Artikel', 'width' => '50px', 'class' => 'text-center'],
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
