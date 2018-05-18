<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Unit extends AuditableModel
{
    protected $fillable = ['name'];

    public function articles() {
        return $this->hasMany(Article::class);
    }

    public function scopeOrderedByName($query) {
        $query->orderBy('name');
    }
}
