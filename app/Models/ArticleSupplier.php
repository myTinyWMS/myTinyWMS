<?php

namespace Mss\Models;

use Mss\Models\Traits\GetAudits;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ArticleSupplier extends Pivot implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use GetAudits;

    protected $guarded = [];
    protected $auditsToDisplay = 20;

    public $incrementing = true;

    protected $ignoredAuditFields = [
        'id', 'article_id'
    ];

    public static function getFieldNames() {
        return [
            'price' => __('Preis'),
            'delivery_time' => __('Lieferzeit'),
            'order_quantity' => __('Bestellmenge'),
            'article_id' => __('Artikel ID'),
            'supplier_id' => __('Lieferant'),
            'order_number' => __('Bestellnummer'),
        ];
    }

    public static function getAuditName() {
        return __('Lieferoptionen');
    }

    /**
     * @return array
     */
    protected function getAuditFormatters() {
        return [
            'price' => function ($value) {
                return formatPrice($value/100);
            },
            'supplier_id' => function ($value) {
                return optional(Supplier::find($value))->name;
            }
        ];
    }

    public function article() {
        return $this->belongsTo(Article::class);
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * @return string
     */
    public function getUpdatedAtColumn() {
        if ($this->pivotParent) {
            return $this->pivotParent->getUpdatedAtColumn();
        }

        return static::UPDATED_AT;
    }

    public function getFormattedForAudit($key) {
        if (array_key_exists($key, $this->getAuditFormatters()) && is_callable($this->getAuditFormatters()[$key])) {
            return $this->getAuditFormatters()[$key]($this->{$key});
        }

        return $this->{$key};
    }
}
