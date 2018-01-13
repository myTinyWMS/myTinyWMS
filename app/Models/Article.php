<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Article extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function unit() {
        return $this->belongsTo(Unit::class);
    }

    public function suppliers() {
        return $this->belongsToMany(Supplier::class);
    }

    public function currentSupplier() {
        return $this->suppliers()->orderBy('created_at', 'DESC')->first();
    }

    public function categories() {
        return $this->belongsToMany(Category::class);
    }
}
