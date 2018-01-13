<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Category extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function articles() {
        return $this->belongsToMany(Article::class);
    }
}
