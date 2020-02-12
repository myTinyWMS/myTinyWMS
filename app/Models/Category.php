<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/*
 * @method static \Illuminate\Database\Query\Builder orderedByName()
 */
class Category extends AuditableModel
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'notes'
    ];

    protected $dates = ['deleted_at'];

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
}
