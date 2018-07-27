<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = ['started_by'];

    public function items() {
        return $this->hasMany(InventoryItem::class);
    }

    public function starter() {
        return $this->belongsTo(User::class, 'started_by');
    }

    public function scopeUnfinished($query) {
        $query->whereHas('items', function ($query) {
            $query->unprocessed();
        });
    }
}
