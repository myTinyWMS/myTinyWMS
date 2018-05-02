<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

/**
 * Class Order
 *
 * @property string $internal_order_number
 * @property Collection $messages
 * @property Collection $items
 * @package Mss\Models
 */
class Order extends AuditableModel
{
    const STATUS_NEW = 0;
    const STATUS_ORDERED = 1;
    const STATUS_PARTIALLY_DELIVERED = 2;
    const STATUS_DELIVERED = 3;
    const STATUS_CANCELLED = 4;
    const STATUS_PAID = 5;

    const STATUSES_OPEN = [Order::STATUS_NEW, Order::STATUS_ORDERED, Order::STATUS_PARTIALLY_DELIVERED];

    const STATUS_TEXTS = [
        self::STATUS_NEW => 'neu',
        self::STATUS_ORDERED => 'bestellt',
        self::STATUS_PARTIALLY_DELIVERED => 'teilweise geliefert',
        self::STATUS_DELIVERED => 'geliefert',
        self::STATUS_CANCELLED => 'storniert',
        self::STATUS_PAID => 'bezahlt'
    ];

    const PAYMENT_STATUS_UNPAID = 0;
    const PAYMENT_STATUS_PAID_WITH_PAYPAL = 1;
    const PAYMENT_STATUS_PAID_WITH_CREDIT_CARD = 2;
    const PAYMENT_STATUS_PAID_WITH_INVOICE = 3;

    const PAYMENT_STATUS_TEXT = [
        self::PAYMENT_STATUS_UNPAID => 'unbezahlt',
        self::PAYMENT_STATUS_PAID_WITH_PAYPAL => 'bezahlt - Paypal',
        self::PAYMENT_STATUS_PAID_WITH_CREDIT_CARD => 'bezahlt - Kreditkarte',
        self::PAYMENT_STATUS_PAID_WITH_INVOICE => 'bezahlt - Rechnung',
    ];

    protected $dates = ['order_date', 'expected_delivery'];

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

    public function messages() {
        return $this->hasMany(OrderMessage::class);
    }

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

    public function scopeStatusOpen($query) {
        $query->whereIn('status', [Order::STATUS_NEW, Order::STATUS_ORDERED, Order::STATUS_PARTIALLY_DELIVERED]);
    }

    public function scopeWithSupplierName($query) {
        $query->addSubSelect('supplier_name', Supplier::select('name')
            ->whereRaw('supplier_id = suppliers.id')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function transformAudit(array $data): array {
        if (Arr::has($data, 'new_values.supplier_id')) {
            $data['old_values']['supplier_id'] = optional(Supplier::find($this->getOriginal('supplier_id')))->name;
            $data['new_values']['supplier_id'] = Supplier::find($this->getAttribute('supplier_id'))->name;
        }

        return $data;
    }
}
