<?php

namespace Mss\Models;

class Supplier extends AuditableModel
{
    protected $fillable = [
        'name', 'email', 'phone', 'contact_person', 'website', 'notes'
    ];

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
}
