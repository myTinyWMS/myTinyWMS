<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderMessage extends Model
{
    use SoftDeletes;

    protected $casts = [
        'attachments' => 'array',
        'sender' => 'array',
        'receiver' => 'array',
    ];

    protected $dates = ['received'];

    protected $fillable = [
        'order_id',
        'user_id',
        'received',
        'sender',
        'receiver',
        'subject',
        'htmlBody',
        'textBody',
        'read',
        'attachments',
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function getSenderAttribute($value) {
        return collect(json_decode($value, true));
    }

    public function getReceiverAttribute($value) {
        return collect(json_decode($value, true));
    }

    public function getAttachmentsAttribute($value) {
        return collect(json_decode($value, true));
    }

    public function scopeUnread($query) {
        $query->where('read', false);
    }

    public function scopeUnassigned($query) {
        $query->whereNull('order_id');
    }

    /**
     * @return Supplier|null
     */
    public function getSupplierBySender() {
        return Supplier::where('email', $this->sender)->first();
    }
}
