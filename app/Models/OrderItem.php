<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

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

    const INVOICE_RECEIVED_TEXT = [
        self::INVOICE_STATUS_RECEIVED => 'erhalten',
        self::INVOICE_STATUS_CHECK => 'in Prüfung',
        self::INVOICE_STATUS_OPEN => 'nicht erhalten'
    ];

    const CONFIRMATION_RECEIVED_TEXT = [
        0 => 'nicht erhalten',
        1 => 'erhalten'
    ];

    protected $ignoredAuditFields = ['order_id'];

    static $auditName = 'Bestellposition';

    protected $fillable = ['article_id', 'price', 'quantity', 'expected_delivery'];

    protected $casts = [
        'confirmation_received' => 'boolean'
    ];

    protected $dates = ['expected_delivery'];

    protected $fieldNames = [
        'invoice_received' => 'Rechnung',
        'confirmation_received' => 'Auftragsbestätigung',
        'price' => 'Preis',
        'quantity' => 'bestellte Menge',
        'expected_delivery' => 'Liefertermin',
        'order_id' => 'Bestellung',
        'article_id' => 'Artikel'
    ];

    /**
     * @return array
     */
    protected function getAuditFormatters() {
        return [
            'invoice_received' => function ($value) {
                return is_numeric($value) ? self::INVOICE_RECEIVED_TEXT[$value] : $value;
            },
            'confirmation_received' => function ($value) {
                return $value ? self::CONFIRMATION_RECEIVED_TEXT[1] : self::CONFIRMATION_RECEIVED_TEXT[0];
            },
            'article_id' => function ($value) {
                return $value ? Article::find($value)->name : null;
            }
        ];
    }

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

    public function scopeNotFullyDelivered($query) {
        $query->whereRaw('COALESCE((SELECT SUM(quantity) FROM delivery_items INNER JOIN deliveries ON deliveries.id = delivery_items.delivery_id WHERE deliveries.order_id = order_items.order_id and delivery_items.article_id = order_items.article_id),0) < order_items.quantity');
    }

    public function scopeFullyDelivered($query) {
        $query->whereRaw('COALESCE((SELECT SUM(quantity) FROM delivery_items INNER JOIN deliveries ON deliveries.id = delivery_items.delivery_id WHERE deliveries.order_id = order_items.order_id and delivery_items.article_id = order_items.article_id),0) = order_items.quantity');
    }

    public function scopeOverDue($query) {
        $query->where('expected_delivery', '<', today());
    }

    /**
     * {@inheritdoc}
     */
    public function transformAudit(array $data): array {
        if (Arr::has($data, 'new_values.invoice_received')) {
            $data['old_values']['invoice_received'] = (array_key_exists($this->getOriginal('invoice_received'), self::INVOICE_RECEIVED_TEXT)) ? self::INVOICE_RECEIVED_TEXT[$this->getOriginal('invoice_received')] : null;
            $data['new_values']['invoice_received'] = self::INVOICE_RECEIVED_TEXT[$this->getAttribute('invoice_received')];
        }

        return $data;
    }
}
