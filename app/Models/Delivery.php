<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Collection;

/**
 * Class Delivery
 *
 * @property integer id
 * @property Order $order
 * @property Collection|DeliveryItem[] $items
 * @package Mss\Models
 */
class Delivery extends AuditableModel
{
    protected $fillable = ['delivery_note_number', 'delivery_date', 'notes', 'order_id'];
    protected $dates = ['delivery_date'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($delivery) {
            /** @var Delivery $delivery */
            if ($delivery->order->isFullyDelivered()) {
                $delivery->order->status = Order::STATUS_DELIVERED;
            } elseif ($delivery->order->isPartiallyDelivered()) {
                $delivery->order->status = Order::STATUS_PARTIALLY_DELIVERED;
            } else {
                $delivery->order->status = Order::STATUS_ORDERED;
            }

            $delivery->order->save();
        });
    }

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
