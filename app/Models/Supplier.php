<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/*
 * @property integer $id
 * @property string $name
 * @method static \Illuminate\Database\Query\Builder orderedByName()
 */
class Supplier extends AuditableModel
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'contact_person', 'website', 'notes', 'accounts_payable_number'
    ];

    protected $dates = ['deleted_at'];

    public static function getFieldNames() {
        return [
            'name' => __('Name'),
            'email' => __('E-Mail'),
            'phone' => __('Telefon'),
            'contact_person' => __('Kontaktperson'),
            'website' => __('Webseite'),
            'notes' => __('Bemerkungen')
        ];
    }

    public static function getAuditName() {
        return __('Lieferant');
    }

    public function articles() {
        return $this->belongsToMany(Article::class);
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function scopeOrderedByName($query) {
        $query->orderBy('name');
    }
}
