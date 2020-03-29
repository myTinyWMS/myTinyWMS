<?php

namespace Mss\DataTables;

use Mss\Models\Article;

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
     * @var bool
     */
    public $paging = false;

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $datatable = parent::dataTable($query);

        $datatable
            ->editColumn('action', 'article.select_list_action')
            ->editColumn('name', function (Article $article) {
                return '<a href="#" onclick="selectArticle('.$article->id.')" data-dismiss="modal">'.$article->name.'</a>';
            });

        $this->rawColumns[] = 'name';
        $datatable->rawColumns($this->rawColumns);

        return $datatable;
    }

    public function query(Article $model) {
        return parent::query($model)->active();
    }

    protected function getHtmlParameters() {
        $parameters = [
            'paging' => $this->paging,
            'deferLoading' => false,
            'order' => [[1, 'asc']],
            'rowGroup' => ['dataSrc' => 'category'],
            'buttons' => []
        ];

        return $parameters;
    }

    protected function getColumns() {
        $cols = parent::getColumns();
        array_shift($cols);
        return $cols;
    }
}
