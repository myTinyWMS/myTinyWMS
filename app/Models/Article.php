<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Article extends AuditableModel
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'notes'
    ];

    protected $casts = [
        'inventory' => 'boolean'
    ];

    protected $dates = ['deleted_at'];

    protected $fieldNames = [
        'name' => 'Name',
        'notes' => 'Bemerkungen'
    ];

    public function unit() {
        return $this->belongsTo(Unit::class);
    }

    public function suppliers() {
        return $this->belongsToMany(Supplier::class)->withTimestamps()->withPivot('order_number', 'price', 'delivery_time', 'order_quantity')->using(SupplierArticle::class);
    }

    public function currentSupplier() {
        return $this->suppliers()->orderBy('created_at', 'DESC')->first();
    }

    public function currentSupplierArticle() {
        return $this->currentSupplier()->pivot;
    }

    public function categories() {
        return $this->belongsToMany(Category::class);
    }

    public function formatQuantity($value) {
        return (!empty($this->unit)) ? $value.' '.$this->unit->name : $value;
    }
}
