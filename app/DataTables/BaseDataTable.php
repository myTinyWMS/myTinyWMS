<?php

namespace Mss\DataTables;

use App;
use Yajra\DataTables\Services\DataTable;

abstract class BaseDataTable extends DataTable
{
    protected $pageLength = 50;

    public function builder() {
        $builder = parent::builder();
        $builder->parameters([
            'dom'     => 'frt<"toolbar">ilp',
            'order'   => [[0, 'asc']],
            'language' => ['url' => asset('js/datatables/German.1.10.13.json')],
            'pageLength' => $this->pageLength,
//            'stateSave' => true,
            'bAutoWidth' => false,
            'lengthMenu' => $this->getLengthMenu()
        ]);
        $builder->setTableAttribute('class', 'table table-hover table-striped table-bordered');

        return $builder;
    }

    protected function getLengthMenu() {
        $values = [50, 100, -1];
        $captions = [50, 100, 'Alle'];

        if (!in_array($this->pageLength, $values)) {
            $values = array_prepend($values, $this->pageLength);
            $captions = array_prepend($captions, $this->pageLength);
        }

        return [$values, $captions];
    }
}