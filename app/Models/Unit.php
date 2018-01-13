<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Unit extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function articles() {
        return $this->hasMany(Article::class);
    }
}
