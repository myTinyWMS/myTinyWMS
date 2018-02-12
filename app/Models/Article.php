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
        'name', 'notes',
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
        return $this->belongsToMany(Supplier::class)->withTimestamps()->withPivot('order_number', 'price', 'delivery_time', 'order_quantity')->using(ArticleSupplier::class);
    }

    public function currentSupplier() {
        return $this->hasOne(Supplier::class, 'id', 'current_supplier_id');
    }

    public function scopeWithCurrentSupplierArticle($query)
    {
        $query->addSubSelect('current_supplier_article_id', ArticleSupplier::select('id')
            ->whereRaw('article_id = articles.id')
            ->latest()
        )->with('currentSupplierArticle');
    }

    public function scopeWithCurrentSupplier($query)
    {
        $query->addSubSelect('current_supplier_id', ArticleSupplier::select('supplier_id')
            ->whereRaw('article_id = articles.id')
            ->latest()
        )->with('currentSupplier');
    }

    public function currentSupplierArticle() {
        return $this->hasOne(ArticleSupplier::class, 'id', 'current_supplier_article_id');
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function articleNotes() {
        return $this->hasMany(ArticleNote::class);
    }

    public function orderItems() {
        return $this->hasMany(OrderItem::class);
    }

    public function formatQuantity($value) {
        return (!empty($value) || $value === 0) ? $value.' '.$this->unit->name : $value;
    }

    public function setNewArticleNumber() {
        $this->article_number = null;
        $this->save();

        $categoryPart = $this->category->id + 10;
        $latestArticleNumber = Article::where('article_number', 'like', $categoryPart.'%')->max('article_number');
        if ($latestArticleNumber) {
            $number = intval(substr($latestArticleNumber, strlen($categoryPart)));
            $newNumber = ++$number;
        } else {
            $newNumber = 1;
        }

        $this->article_number = $categoryPart.sprintf('%03d', $newNumber);
        $this->save();
    }
}
