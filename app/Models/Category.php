<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Collective\Html\Eloquent\FormAccessible;

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
}
