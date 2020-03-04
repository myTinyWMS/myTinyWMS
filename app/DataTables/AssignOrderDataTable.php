<?php

namespace Mss\DataTables;

class AssignOrderDataTable extends OrderDataTable {

    protected $actionView = 'order_messages.order_list_action';
    protected $pageLength = 30;
    protected $paging = true;

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            ['data' => 'internal_order_number', 'name' => 'internal_order_number', 'title' => __('Bestellnummer')],
            ['data' => 'supplier', 'name' => 'supplier', 'title' => __('Lieferant'), 'visible' => false],
            ['data' => 'items', 'name' => 'items', 'title' => __('Artikel'), 'searchable' => false],
            ['data' => 'status', 'name' => 'status', 'title' => __('Status')]
        ];
    }

}