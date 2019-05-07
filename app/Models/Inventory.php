<?php

namespace Mss\Models;

class Inventory extends AuditableModel
{
    protected $fillable = ['started_by'];

    public function items() {
        return $this->hasMany(InventoryItem::class);
    }

    public function starter() {
        return $this->belongsTo(User::class, 'started_by');
    }

    public function isFinished() {
        return $this->items->filter(function ($item) {
            return empty($item->processed_at);
        })->count() === 0;
    }

    public function scopeUnfinished($query) {
        $query->whereHas('items', function ($query) {
            $query->unprocessed();
        });
    }

    public function scopeFinished($query) {
        $query->whereDoesntHave('items', function ($query) {
            $query->unprocessed();
        });
    }
}
