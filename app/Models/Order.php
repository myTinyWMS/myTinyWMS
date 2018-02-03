<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const STATUS_ORDERED = 0;
    const STATUS_PARTIALLY_DELIVERED = 1;
    const STATUS_DELIVERED = 2;
    const STATUS_CANCELLED = 3;

    protected $dates = ['order_date', 'expected_delivery'];

    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }
}
