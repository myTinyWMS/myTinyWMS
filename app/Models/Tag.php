<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;

/*
 * @method static \Illuminate\Database\Query\Builder orderedByName()
 */
class Tag extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];

    public function articles() {
        return $this->morphedByMany(Article::class, 'taggable');
    }

    public function scopeOrderedByName($query) {
        $query->orderBy('name');
    }
}
