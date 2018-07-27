<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
        'processed_at'
    ];

    protected $fillable = ['article_id', 'inventory_id'];

    public function inventory() {
        return $this->belongsTo(Inventory::class);
    }

    public function article() {
        return $this->belongsTo(Article::class);
    }

    public function processor() {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopeUnprocessed($query) {
        $query->whereNull('processed_at');
    }
}
