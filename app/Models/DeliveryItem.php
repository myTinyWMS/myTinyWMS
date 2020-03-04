<?php

namespace Mss\Models;

class DeliveryItem extends AuditableModel
{
    protected $fillable = ['article_id', 'quantity'];

    public static function getFieldNames() {
        return [];
    }

    public static function getAuditName() {
        return __('Lieferposition');
    }

    public function article() {
        return $this->belongsTo(Article::class);
    }

    public function delivery() {
        return $this->belongsTo(Delivery::class);
    }

    public function articleChangeLogs() {
        return $this->hasMany(ArticleQuantityChangelog::class);
    }

    public function orderItem() {
        return $this->hasOneThrough(
            OrderItem::class,
            Delivery::class,
            'id',
            'order_id',
            'delivery_id',
            'order_id'
        );
    }
}
