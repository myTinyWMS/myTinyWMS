<?php

namespace Mss\Models;


/**
 * Class DeliveryItem
 *
 * @property integer id
 * @property Delivery $delivery
 * @property ArticleQuantityChangelog $articleChangeLog
 * @package Mss\Models
 */
class DeliveryItem extends AuditableModel
{
    protected $fillable = ['article_id', 'quantity'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($deliveryItem) {
            /** @var DeliveryItem $deliveryItem */

            $delivery = $deliveryItem->delivery;

            if ($delivery && $delivery->items()->count() == 0) {
                $delivery->delete();
            }
        });
    }

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

    public function articleChangeLog() {
        return $this->hasOne(ArticleQuantityChangelog::class);
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
