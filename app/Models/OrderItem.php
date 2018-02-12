<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['article_id', 'price', 'quantity'];

    public function article() {
        return $this->belongsTo(Article::class);
    }

    public function order() {
        return $this->belongsTo(Order::class);
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
