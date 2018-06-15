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

    protected $fieldNames = [
        'name' => 'Name',
        'notes' => 'Bemerkungen'
    ];

    public function articles() {
        return $this->hasMany(Article::class);
    }

    public function scopeOrderedByName($query) {
        $query->orderBy('name');
    }

    public function scopeWithActiveArticles($query) {
        $query->with(['articles' => function ($query) {
            $query->active()->orderedByArticleNumber();
        }]);
    }
}
