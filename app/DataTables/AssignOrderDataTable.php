<?php

namespace Mss\DataTables;

class AssignOrderDataTable extends OrderDataTable {

    protected $actionView = 'order_messages.order_list_action';
    protected $pageLength = 10;

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
            ['data' => 'status', 'name' => 'status', 'title' => 'Status']
        ];
    }

}