<?php

namespace Mss\Models;

class Delivery extends AuditableModel
{
    protected $fillable = ['delivery_note_number', 'delivery_date', 'notes'];
    protected $dates = ['delivery_date'];

    public static function getFieldNames() {
        return [];
    }

    public static function getAuditName() {
        return __('Lieferung');
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function items() {
        return $this->hasMany(DeliveryItem::class);
    }
}
