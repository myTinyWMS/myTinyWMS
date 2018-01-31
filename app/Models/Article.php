<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mss\Models\Traits\Taggable;
use OwenIt\Auditing\Contracts\Auditable;

class Article extends AuditableModel
{
    use SoftDeletes, Taggable;

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

    public function quantityChangelogs() {
        return $this->hasMany(ArticleQuantityChangelog::class);
    }

    public function tags() {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function unit() {
        return $this->belongsTo(Unit::class);
    }

    public function suppliers() {
        return $this->belongsToMany(Supplier::class)->withTimestamps()->withPivot('order_number', 'price', 'delivery_time', 'order_quantity')->using(SupplierArticle::class);
    }

    public function currentSupplier() {
        return $this->suppliers->sortByDesc('created_at')->first();
    }

    public function currentSupplierArticle() {
        return $this->currentSupplier()->pivot;
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function articleNotes() {
        return $this->hasMany(ArticleNote::class);
    }

    public function formatQuantity($value) {
        return (!empty($value) || $value === 0) ? $value.' '.$this->unit->name : $value;
    }
}
