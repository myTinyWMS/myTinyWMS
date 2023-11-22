<?php

namespace Mss\DataTables;

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
            ->addColumn('article_number', function (ArticleGroup $articleGroup) {
                return $articleGroup->getArticleNumber();
            })
            ->addColumn('article', function (ArticleGroup $articleGroup) {
                return $articleGroup->items->count();
            })
            ->addColumn('items', function (ArticleGroup $articleGroup) {
                return view('article_group.list_items', compact('articleGroup'))->render();
            })
            ->editColumn('name', function (ArticleGroup $articleGroup) {
                return link_to_route('article-group.show', $articleGroup->name, ['article_group' => $articleGroup], ['target' => '_blank']);
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
                'paging' => true,
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
            ['data' => 'article_number', 'name' => 'article_number', 'title' => __('Interne Artikelnummer')],
            ['data' => 'external_article_number', 'name' => 'external_article_number', 'title' => __('Externe Artikelnummer')],
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
