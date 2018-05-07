<?php

namespace Mss\DataTables;

use Mss\Models\Article;

class SelectArticleDataTable extends ArticleDataTable
{
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

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    /*public function html()
    {
        return $this->builder()
            ->minifiedAjax()
            ->columns($this->getColumns())
            ->parameters($this->getHtmlParameters());
    }*/

}
