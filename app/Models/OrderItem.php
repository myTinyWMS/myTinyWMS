<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public function article() {
        return $this->belongsTo(Article::class);
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }
}
