<?php

namespace Mss\Models;

class Order extends AuditableModel
{
    const STATUS_NEW = 0;
    const STATUS_ORDERED = 1;
    const STATUS_PARTIALLY_DELIVERED = 2;
    const STATUS_DELIVERED = 3;
    const STATUS_CANCELLED = 4;
    const STATUS_PAID = 5;

    const STATUS_TEXTS = [
        self::STATUS_NEW => 'neu',
        self::STATUS_ORDERED => 'bestellt',
        self::STATUS_PARTIALLY_DELIVERED => 'teilweise geliefert',
        self::STATUS_DELIVERED => 'geliefert',
        self::STATUS_CANCELLED => 'storniert',
        self::STATUS_PAID => 'bezahlt'
    ];

    protected $dates = ['order_date', 'expected_delivery'];

    protected $casts = [
        'confirmation_received' => 'boolean',
    ];

    protected $fieldNames = [
        'status' => 'Status',
        'total_cost' => 'Gesamtkosten',
        'shipping_cost' => 'Versandkosten',
        'order_date' => 'Bestelldatum',
        'expected_delivery' => 'Liefertermin',
        'external_order_number' => 'Bestellnummer des Lieferanten',
        'internal_order_number' => 'interne Bestellnummer',
        'confirmation_received' => 'Auftragsbestätigung erhalten'
    ];

    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function deliveries() {
        return $this->hasMany(Delivery::class);
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

    public function isFullyDelivered() {
        $this->fresh();

        return $this->items->reject(function ($item) {
            return ($item->getQuantityDelivered() >= $item->quantity);
        })->count() === 0;
    }
}
