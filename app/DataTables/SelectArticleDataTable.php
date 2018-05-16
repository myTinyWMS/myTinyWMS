<?php

namespace Mss\DataTables;

class SelectArticleDataTable extends ArticleDataTable
{
    const STATUS_COL_ID = 16;
    const CATEGORY_COL_ID = 15;
    const TAGS_COL_ID = 14;
    const SUPPLIER_COL_ID = 12;

    /**
     * @var bool
     */
    protected $sortingEnabled = true;

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $datatable = parent::dataTable($query);

        $datatable->editColumn('action', 'article.select_list_action');

        return $datatable;
    }

    protected function getHtmlParameters() {
        $parameters = [
            'paging' => false,
            'deferLoading' => false,
            'order' => [[self::SUPPLIER_COL_ID, 'asc']],
            'rowGroup' => ['dataSrc' => 'category']
        ];

        return $parameters;
    }

    protected function getColumns() {
        $cols = parent::getColumns();
        array_shift($cols);
        return $cols;
    }
}
