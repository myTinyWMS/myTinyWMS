<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderItem
 *
 * @property Article $article
 * @property Order $order
 * @package Mss\Models
 */
class OrderItem extends AuditableModel
{
    const INVOICE_STATUS_OPEN = 0;
    const INVOICE_STATUS_RECEIVED = 1;
    const INVOICE_STATUS_CHECK = 2;

    protected $fillable = ['article_id', 'price', 'quantity', 'expected_delivery'];

    protected $casts = [
        'confirmation_received' => 'boolean'
    ];

    protected $dates = ['expected_delivery'];

    /**
     * @mixin Article
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function article() {
        return $this->belongsTo(Article::class);
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function deliveryItems() {
        return $this->hasManyThrough(
            DeliveryItem::class,
            Delivery::class,
            'order_id',
            'delivery_id',
            'order_id',
            'id'
        )->where('article_id', $this->article_id);
    }

    public function getPriceAttribute($value) {
        return !empty($value) ? $value / 100 : 0;
    }

    public function getQuantityDelivered() {
        return $this->order->deliveries->map(function ($delivery) {
            $deliveryItem = $delivery->items->where('article_id', $this->article_id)->first();
            return $deliveryItem ? $deliveryItem->quantity : 0;
        })->sum();
    }
}
