<?php

namespace Mss\Models;

/**
 * Class ArticleQuantityChangelog
 *
 * @property integer id
 * @property integer type
 * @property integer change
 * @property string note
 * @property Article $article
 * @property DeliveryItem $deliveryItem
 * @package Mss\Models
 */
class ArticleQuantityChangelog extends AuditableModel
{
    const TYPE_START = 0;
    const TYPE_INCOMING = 1;
    const TYPE_OUTGOING = 2;
    const TYPE_CORRECTION = 3;
    const TYPE_COMMENT = 6;
    const TYPE_INVENTORY = 7;
    const TYPE_REPLACEMENT_DELIVERY = 8;
    const TYPE_OUTSOURCING = 9;
    const TYPE_SALE_TO_THIRD_PARTIES = 10;
    const TYPE_TRANSFER = 11;
    const TYPE_RETOURE = 12;

    protected $fillable = ['created_at', 'updated_at', 'user_id', 'type', 'change', 'new_quantity', 'note', 'delivery_item_id', 'unit_id', 'article_id', 'related_id'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($changelog) {
            /** @var ArticleQuantityChangelog $changelog */

            if ($changelog->deliveryItem) {
                $changelog->deliveryItem->delete();
            }

            $changelog->article->resetQuantityFromChangelog($changelog);
        });
    }

    public static function getFieldNames() {
        return [];
    }

    public static function getAuditName() {
        return __('Artikel-Bestandsänderung');
    }

    public static function getAbbreviation($key) {
        $abbreviations = collect([
            self::TYPE_INCOMING => __('WE'),
            self::TYPE_OUTGOING => __('WA'),
            self::TYPE_CORRECTION => __('KB'),
            self::TYPE_INVENTORY => __('INV'),
            self::TYPE_OUTSOURCING => __('AL'),
            self::TYPE_REPLACEMENT_DELIVERY => __('EL'),
            self::TYPE_SALE_TO_THIRD_PARTIES => __('VaF'),
            self::TYPE_TRANSFER => __('UB'),
            self::TYPE_COMMENT => __('KO'),
            self::TYPE_RETOURE => __('RE'),
        ]);

        return $abbreviations->get($key, __('Unbekannt'));
    }

    public static function getAvailableTypes() {
        return [
            self::TYPE_START,
            self::TYPE_INCOMING,
            self::TYPE_OUTGOING,
            self::TYPE_CORRECTION,
            self::TYPE_COMMENT,
            self::TYPE_INVENTORY,
            self::TYPE_REPLACEMENT_DELIVERY,    // no quantity change!
            self::TYPE_OUTSOURCING,             // no quantity change!
            self::TYPE_SALE_TO_THIRD_PARTIES,
            self::TYPE_TRANSFER,
            self::TYPE_RETOURE,
        ];
    }

    public function article() {
        return $this->belongsTo(Article::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function unit() {
        return $this->belongsTo(Unit::class);
    }

    public function deliveryItem() {
        return $this->belongsTo(DeliveryItem::class);
    }

    public function related() {
        return $this->belongsTo(ArticleQuantityChangelog::class, 'related_id', 'id');
    }
}
