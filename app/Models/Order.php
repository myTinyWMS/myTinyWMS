<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends AuditableModel
{
    const STATUS_NEW = 0;
    const STATUS_ORDERED = 1;
    const STATUS_PARTIALLY_DELIVERED = 2;
    const STATUS_DELIVERED = 3;
    const STATUS_CANCELLED = 4;

    protected $dates = ['order_date', 'expected_delivery'];

    /*
     * @todo set correct field names!!!!
     */
    protected $fieldNames = [
        'external_order_number' => 'Name',
        'internal_order_number' => 'Bemerkungen'
    ];

    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * @return string
     */
    public function getNextInternalOrderNumber() {
        $lastOrder = Order::where('internal_order_number', 'like', date("ymd").'%')->orderBy('internal_order_number', 'desc')->first();
        if ($lastOrder) {
            $latestNumber = intval(substr($lastOrder->internal_order_number, 6));
        } else {
            $latestNumber = 0;
        }

        return date("ymd").($latestNumber+1);
    }

    public function getTotalCostAttribute($value) {
        return !empty($value) ? $value / 100 : 0;
    }

    public function getShippingCostAttribute($value) {
        return !empty($value) ? $value / 100 : 0;
    }
}
