<?php

namespace Mss\DataTables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Mss\Models\Article;
use Mss\Models\Order;
use Mss\Models\OrderItem;

class ToOrderDataTable extends ArticleDataTable
{
    protected $dom = '<"table-toolbar"<"flex mb-4"<"#table-filter">>r<"table-toolbar-right">><"table-wrapper"<"fix-head-bg"><"table-content"t><"table-footer"<"table-footer-actions">ip>>';

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = parent::dataTable($query);
        $dataTable->setRowClass(function ($article) {
            return '';
        })
        ->setRowAttr([
            'data-supplier' => function ($article) {
                if (!$article->currentSupplier) {
                    Log::error('Article without supplier!', ['article' => $article->id]);
                    return null;
                }
                return $article->currentSupplier->id;
            }
        ])
        ->editColumn('checkbox',function (Article $article) {
            if (!Auth::user()->hasPermissionTo('order.create')) return '';

            return '<div class="i-checks">
                        <label id="new_order_'.$article->id.'">
                            <input type="checkbox" value="'.$article->id.'" name="article[]" />
                        </label>
                    </div>';
        });

        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \Mss\Models\Article $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Article $model)
    {
        return $model->newQuery()
            ->withCurrentSupplierArticle()->withCurrentSupplier()->withCurrentSupplierName()->withAverageUsage(12)->withAverageUsage(3)->withLastReceipt()
            ->with(['category', 'suppliers', 'unit', 'tags'])
            ->where(function (Builder $query) {
                $query->whereRaw('quantity <= min_quantity')->orWhere('quantity', '<', 0);
            })
            ->where('min_quantity', '>', -1)
            ->where('status', Article::STATUS_ACTIVE)
            ->whereDoesntHave('category', function (Builder $query) {
                $query->where('show_in_to_order_on_dashboard', false);
            })
            ->whereDoesntHave('orderItems', function ($query) {
                $query->notFullyDelivered()->whereHas('order', function ($query) {
                    $query->statusOpen();
                });
            });
    }
}
