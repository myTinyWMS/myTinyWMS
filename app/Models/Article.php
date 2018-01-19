<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Article extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

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
