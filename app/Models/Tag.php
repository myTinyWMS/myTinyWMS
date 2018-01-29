<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public $timestamps = false;

    public function articles() {
        return $this->morphedByMany(Article::class, 'taggable');
    }
}
