<?php

namespace Mss\DataTables;

use Carbon\Carbon;
use Mss\Models\Order;
use Mss\Models\OrderItem;

class OrderDataTable extends BaseDataTable
{
    /**
     * @var string
     */
    protected $actionView = 'order.list_action';

    /**
     * @var bool
     */
    protected $paging = false;

    /**
     * @var array
     */
    protected $defaultStatusFilter = Order::STATES_OPEN;

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
                return $order->supplier ? $order->supplier->name : '<span class="italic text-gray-600">leer</span>';
            })
            ->orderColumn('supplier', 'supplier_name $1')
            ->editColumn('order_date', function (Order $order) {
                if (empty($order->order_date)) {
                    return '';
                }

                if ($order->order_date->diffInDays(Carbon::now()) < 1) {
                    return 'heute';
                }

                return $order->order_date->format('d.m.Y');
            })
            ->editColumn('expected_delivery', function (Order $order) {
                /* @var $expectedDelivery Carbon */
                $expectedDelivery = $order->items->max('expected_delivery');
                $output = $expectedDelivery ? $expectedDelivery->format('d.m.Y') : '';

                $overdueItems = $order->items->filter(function ($orderItem) {
                    /** @var OrderItem $orderItem */
                    return ($orderItem->expected_delivery < now() && $orderItem->getQuantityDelivered() < $orderItem->quantity);
                });

                if ($overdueItems->count()) {
                    $output .= '<br><span class="text-red-400 text-sm font-bold">überfällig</span>';
                }

                return $output;
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

                    case 'check':
                        $query->whereRaw('(SELECT COUNT(*) FROM order_items WHERE order_id = orders.id AND invoice_received = 2) > 0');
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
                if (!isset(request('columns')[3]['search']) && !empty($this->defaultStatusFilter)) {
                    $query->whereIn('status', $this->defaultStatusFilter);
                }

            }, true)
            ->addColumn('items', function ($order) {
                return view('order.list_items', compact('order'))->render();
            })
            ->editColumn('status', 'order.status')
            ->addColumn('action', $this->actionView)
            ->rawColumns(['action', 'supplier', 'status', 'order_date', 'expected_delivery', 'internal_order_number', 'invoice_status', 'confirmation_status', 'items']);
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
            ->with(['items.order.deliveries.items', 'items.article' => function ($query) {
                $query->withCurrentSupplierArticle();
            }, 'supplier', 'messages']);
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
                'paging' => $this->paging,
                'order'   => [[1, 'asc']],
                'rowGroup' => ['dataSrc' => 'supplier']
            ])
            ->addAction(['title' => __('Aktion'), 'width' => '100px', 'class' => 'action-col']);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            ['data' => 'internal_order_number', 'name' => 'internal_order_number', 'title' => __('Bestellnummer'), 'width' => '160px'],
            ['data' => 'supplier', 'name' => 'supplier', 'title' => __('Lieferant'), 'visible' => false],
            ['data' => 'items', 'name' => 'items', 'title' => __('Artikel')],
            ['data' => 'status', 'name' => 'status', 'title' => __('Bestellstatus'), 'width' => '150px', 'class' => 'text-center'],
            ['data' => 'confirmation_status', 'name' => 'confirmation_status', 'title' => __('AB'), 'width' => '110px', 'class' => 'text-center', 'orderable' => false],
            ['data' => 'invoice_status', 'name' => 'invoice_status', 'title' => __('Rechnung'), 'width' => '110px', 'class' => 'text-center', 'orderable' => false],
            ['data' => 'order_date', 'name' => 'order_date', 'title' => __('Bestelldatum'), 'class' => 'text-right', 'searchable' => false, 'width' => '90px'],
            ['data' => 'expected_delivery', 'name' => 'expected_delivery', 'title' => __('Lieferdatum'), 'class' => 'text-right', 'searchable' => false, 'width' => '90px'],
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
