<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class OrderMessage extends AuditableModel
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
        return collect(json_decode($value, true))->transform(function ($attachment) {
            if(stripos($attachment['orgFileName'], "=?utf-8?b?") !== false) {
                $attachment['orgFileName'] = str_ireplace("=?utf-8?B?", "", $attachment['orgFileName']);
                $attachment['orgFileName'] = str_replace("==?=", "", $attachment['orgFileName']);
                $attachment['orgFileName'] = utf8_encode(base64_decode($attachment['orgFileName']));
            }

            return $attachment;
        });
    }

    public function scopeUnread($query) {
        $query->where('read', false);
    }

    public function scopeUnassigned($query) {
        $query->whereNull('order_id');
    }

    public function scopeAssigned($query) {
        $query->whereNotNull('order_id');
    }

    /**
     * @return Supplier|null
     */
    public function getSupplierBySender() {
        return Supplier::where('email', $this->sender)->first();
    }
}
