<?php

namespace Mss\DataTables;

use App;
use Yajra\DataTables\Services\DataTable;

abstract class BaseDataTable extends DataTable
{
    public function builder() {
        $builder = parent::builder();
        $builder->parameters([
            'dom'     => 'frtilp',
            'order'   => [[0, 'asc']],
            'language' => ['url' => asset('js/datatables/German.1.10.13.json')],
            'pageLength' => 25,
            'lengthMenu' => [[25, 50, 100, -1], [25, 50, 100, 'Alle']]
        ]);
        $builder->setTableAttribute('class', 'table table-hover');

        return $builder;
    }
}