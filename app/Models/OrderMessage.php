<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;

class OrderMessage extends Model
{
    protected $casts = [
        'attachments' => 'array',
        'sender' => 'array',
        'receiver' => 'array',
    ];

    protected $fillable = [
        'order_id',
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
}
