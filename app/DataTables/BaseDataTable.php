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
            'dom'     => '<"table-toolbar"<"flex mb-4"<"#table-filter"><"table-search"f>>r<"table-toolbar-right">><"table-wrapper"<"fix-head-bg"><"table-content"t><"table-footer"<"table-footer-actions">ip>>',
            'order'   => [[0, 'asc']],
            'language' => ['url' => asset('js/datatables/German.1.10.13.json'), 'searchPlaceholder' => 'Suche'],
            'pageLength' => $this->pageLength,
//            'stateSave' => true,
            'bAutoWidth' => false,
            'lengthMenu' => $this->getLengthMenu()
        ]);
        $builder->setTableAttribute('class', 'table');

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