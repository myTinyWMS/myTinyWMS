<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Collective\Html\Eloquent\FormAccessible;
use Illuminate\Support\Facades\DB;

/**
 * Class Category
 *
 * @property integer id
 * @property string name
 * @method static \Illuminate\Database\Query\Builder orderedByName()
 * @package Mss\Models
 */
class Category extends AuditableModel
{
    use SoftDeletes, FormAccessible;

    protected $fillable = [
        'name', 'notes', 'show_in_to_order_on_dashboard'
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'show_in_to_order_on_dashboard' => 'boolean'
    ];

    public static function getFieldNames() {
        return [
            'name' => __('Name'),
            'notes' => __('Bemerkungen')
        ];
    }

    public static function getAuditName() {
        return __('Kategorie');
    }

    public function articles() {
        return $this->hasMany(Article::class);
    }

    public function scopeOrderedByName($query) {
        $query->orderBy('name');
    }

    public function scopeWithActiveArticles($query) {
        $query->with(['articles' => function ($query) {
            $query->enabled()->orderedByArticleNumber();
        }]);
    }

    public function formShowInToOrderOnDashboardAttribute() {
        return $this->show_in_to_order_on_dashboard ? 1 : 0;
    }

    /**
     * Get the total inventory value for this category (quantity * price from current supplier)
     * Uses direct database query for optimal performance with large datasets
     * @return float
     */
    public function getInventoryValue()
    {
        // Get all articles with their latest supplier article
        $result = DB::table('articles')
            ->leftJoin('article_supplier', function($join) {
                // Join with the latest (most recently created) supplier article for each article
                $join->on('articles.id', '=', 'article_supplier.article_id')
                    ->whereRaw('article_supplier.created_at = (
                        SELECT MAX(created_at) 
                        FROM article_supplier 
                        WHERE article_id = articles.id
                    )');
            })
            ->where('articles.category_id', $this->id)
            ->whereNull('articles.deleted_at')
            ->selectRaw('SUM(articles.quantity * IFNULL(article_supplier.price, 0)) as total')
            ->first();

        // price is stored in cents, so divide by 100
        $total = $result->total ?? 0;

        return $total > 0 ? $total / 100 : 0;
    }
}
