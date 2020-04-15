<?php

namespace Mss\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

/**
 * Class Order
 *
 * @property integer $id
 * @property string $internal_order_number
 * @property integer $status
 * @property Supplier $supplier
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

    const STATES_OPEN = [Order::STATUS_NEW, Order::STATUS_ORDERED, Order::STATUS_PARTIALLY_DELIVERED];

    const PAYMENT_STATUS_UNPAID = 0;
    const PAYMENT_STATUS_PAID_WITH_PAYPAL = 1;
    const PAYMENT_STATUS_PAID_WITH_CREDIT_CARD = 2;
    const PAYMENT_STATUS_PAID_WITH_INVOICE = 3;
    const PAYMENT_STATUS_PAID_WITH_AUTOMATIC_DEBIT_TRANSFER = 4;
    const PAYMENT_STATUS_PAID_WITH_PRE_PAYMENT = 5;

    protected $dates = ['order_date', 'expected_delivery'];

    protected $ignoredAuditFields = ['supplier_id'];

    public static function getPaymentStatusText() {
        return [
            self::PAYMENT_STATUS_UNPAID => __('unbezahlt'),
            self::PAYMENT_STATUS_PAID_WITH_PAYPAL => __('Paypal'),
            self::PAYMENT_STATUS_PAID_WITH_CREDIT_CARD => __('Kreditkarte'),
            self::PAYMENT_STATUS_PAID_WITH_INVOICE => __('Rechnung'),
            self::PAYMENT_STATUS_PAID_WITH_AUTOMATIC_DEBIT_TRANSFER => __('Bankeinzug'),
            self::PAYMENT_STATUS_PAID_WITH_PRE_PAYMENT => __('Vorkasse'),
        ];
    }

    public static function getStatusTexts() {
        return [
            self::STATUS_NEW => __('neu'),
            self::STATUS_ORDERED => __('bestellt'),
            self::STATUS_PARTIALLY_DELIVERED => __('teilweise geliefert'),
            self::STATUS_DELIVERED => __('geliefert'),
            self::STATUS_CANCELLED => __('storniert'),
            self::STATUS_PAID => __('bezahlt')
        ];
    }

    public static function getFieldNames() {
        return [
            'notes' => __('Bemerkungen'),
            'status' => __('Status'),
            'payment_status' => __('Bezahlmethode'),
            'total_cost' => __('Gesamtkosten'),
            'shipping_cost' => __('Versandkosten'),
            'order_date' => __('Bestelldatum'),
            'expected_delivery' => __('Liefertermin'),
            'external_order_number' => __('Bestellnummer des Lieferanten'),
            'internal_order_number' => __('interne Bestellnummer'),
            'confirmation_received' => __('Auftragsbestätigung erhalten')
        ];
    }

    public static function getAuditName() {
        return __('Bestellung');
    }

    public function messages() {
        return $this->hasMany(OrderMessage::class);
    }

    public function items() {
        return $this->hasMany(OrderItem::class)->orderBy('id');
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
        /**
         * adding "+0" to the order by field forces mysql to sort natural
         */
        $lastOrder = Order::where('internal_order_number', 'like', Carbon::now()->format('ymd').'%')->orderByRaw('internal_order_number+0 DESC')->first();
        if ($lastOrder) {
            $latestNumber = intval(substr($lastOrder->internal_order_number, 6));
        } else {
            $latestNumber = 0;
        }

        return Carbon::now()->format('ymd').($latestNumber+1);
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

    public function isPartiallyDelivered() {
        $this->fresh();

        $isPartiallyDelivery = $this->items->reject(function ($item) {
            return ($item->getQuantityDelivered() >= $item->quantity);
        })->count() < $this->items->count();
        return $isPartiallyDelivery && !$this->isFullyDelivered();
    }

    public function scopeStatusOpen($query) {
        $query->whereIn('status', [Order::STATUS_NEW, Order::STATUS_ORDERED, Order::STATUS_PARTIALLY_DELIVERED]);
    }

    public function scopeWithSupplierName($query) {
        $query->addSubSelect('supplier_name', Supplier::select('name')
            ->whereRaw('supplier_id = suppliers.id')
        );
    }

    public function scopeOverdue($query) {
        $query->whereHas('items', function ($query) {
            $query->overdue()->notFullyDelivered();
        });
    }

    /**
     * @return Carbon
     */
    public function getOldestOverdueDate() {
        return $this->items->filter(function ($item) {
            return $item->getQuantityDelivered() != $item->quantity;
        })->min('expected_delivery');
    }

    /**
     * {@inheritdoc}
     */
    public function transformAudit(array $data): array {
        if (Arr::has($data, 'new_values.supplier_id')) {
            $data['old_values']['supplier_id'] = optional(Supplier::find($this->getOriginal('supplier_id')))->name;
            $data['new_values']['supplier_id'] = Supplier::find($this->getAttribute('supplier_id'))->name;
        }

        if (Arr::has($data, 'new_values.status')) {
            $data['old_values']['status'] = (array_key_exists($this->getOriginal('status'), Order::getStatusTexts())) ? Order::getStatusTexts()[$this->getOriginal('status')] : null;
            $data['new_values']['status'] = Order::getStatusTexts()[$this->getAttribute('status')];
        }

        if (Arr::has($data, 'new_values.payment_status')) {
            $data['old_values']['payment_status'] = (array_key_exists($this->getOriginal('payment_status'), Order::getPaymentStatusText())) ? Order::getPaymentStatusText()[$this->getOriginal('payment_status')] : null;
            $data['new_values']['payment_status'] = Order::getPaymentStatusText()[$this->getAttribute('payment_status')];
        }

        return $data;
    }

    public function getAllAudits() {
        $orderItemAudits = $this->items->map(function ($item) {
            return $item->getAudits()->transform(function ($audit) use ($item) {
                $audit['name'] .= ' #'.$item->article->article_number;

                return $audit;
            });
        })->flatten(1);

        $orderMessageAudits = $this->messages->map(function ($message) {
            return $message->getAudits()->where('event', 'updated')->transform(function ($audit) use ($message) {
                $audit['name'] .= ' #'.$message->id;

                return $audit;
            });
        })->flatten(1);

        $audits = $this->getAudits();

        return collect($audits->toArray())->merge($orderItemAudits)->merge($orderMessageAudits)->sortByDesc('timestamp');
    }
}
