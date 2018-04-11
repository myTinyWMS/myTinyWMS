<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleQuantityChangelog extends Model
{
    const TYPE_START = 0;
    const TYPE_INCOMING = 1;
    const TYPE_OUTGOING = 2;
    const TYPE_CORRECTION = 3;
    const TYPE_COMMENT = 6;
    const TYPE_INVENTORY = 7;

    protected $fillable = ['created_at', 'updated_at', 'user_id', 'type', 'change', 'new_quantity', 'note', 'delivery_item_id'];

    public function article() {
        return $this->belongsTo(Article::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function deliveryItem() {
        return $this->belongsTo(DeliveryItem::class);
    }
}
