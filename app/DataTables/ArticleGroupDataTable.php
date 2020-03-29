<?php

namespace Mss\DataTables;

use Carbon\Carbon;
use Mss\Models\ArticleGroup;

class ArticleGroupDataTable extends BaseDataTable
{
    /**
     * @var string
     */
    protected $actionView = 'article_group.list_action';

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
            ->addColumn('article', function (ArticleGroup $articleGroup) {
                return $articleGroup->items->count();
            })
            ->addColumn('items', function (ArticleGroup $articleGroup) {
                return view('article_group.list_items', compact('articleGroup'))->render();
            })
            ->addColumn('action', $this->actionView)
            ->rawColumns(['action', 'items']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \Mss\Models\ArticleGroup $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ArticleGroup $model)
    {
        return $model->newQuery()
            ->with(['items.article' => function ($query) {
                $query->withCurrentSupplierArticle();
            }]);
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
                'buttons' => []
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
            ['data' => 'name', 'name' => 'name', 'title' => __('Name')],
            ['data' => 'items', 'name' => 'items', 'title' => __('Artikel')],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'ArticleGroups_' . date('YmdHis');
    }
}
