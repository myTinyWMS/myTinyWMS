<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/*
 * @method static \Illuminate\Database\Query\Builder orderedByName()
 */
class Supplier extends AuditableModel
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'contact_person', 'website', 'notes', 'accounts_payable_number'
    ];

    protected $dates = ['deleted_at'];

    protected $fieldNames = [
        'name' => 'Name',
        'email' => 'E-Mail',
        'phone' => 'Telefon',
        'contact_person' => 'Kontaktperson',
        'website' => 'Webseite',
        'notes' => 'Bemerkungen'
    ];

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
