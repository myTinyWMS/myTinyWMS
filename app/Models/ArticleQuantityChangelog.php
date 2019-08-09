<?php

namespace Mss\Models;

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

    protected $fillable = ['created_at', 'updated_at', 'user_id', 'type', 'change', 'new_quantity', 'note', 'delivery_item_id', 'unit_id', 'article_id', 'related_id'];

    static $auditName = 'Artikel-Bestandsänderung';

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
