<?php

namespace Mss\DataTables;

class AssignOrderDataTable extends OrderDataTable {

    protected $actionView = 'order_messages.order_list_action';
    protected $pageLength = 30;
    protected $paging = true;
    protected $defaultStatusFilter = null;

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            ['data' => 'internal_order_number', 'name' => 'internal_order_number', 'title' => 'Bestellnummer'],
            ['data' => 'supplier', 'name' => 'supplier', 'title' => 'Lieferant', 'visible' => false],
            ['data' => 'items', 'name' => 'items', 'title' => 'Artikel', 'searchable' => false],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status']
        ];
    }

}