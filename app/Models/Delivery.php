<?php

namespace Mss\Models;

class Delivery extends AuditableModel
{
    protected $fillable = ['delivery_note_number', 'delivery_date', 'notes'];
    protected $dates = ['delivery_date'];

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function items() {
        return $this->hasMany(DeliveryItem::class);
    }
}
