<?php

namespace Mss\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mss\Models\Traits\Taggable;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class Article
 *
 * @method static Builder active()
 * @package Mss\Models
 */
class Article extends AuditableModel
{
    use SoftDeletes, Taggable;

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    protected $fillable = ['name', 'article_number', 'unit_id', 'category_id', 'status', 'quantity', 'min_quantity', 'usage_quantity', 'issue_quantity', 'sort_id', 'inventory', 'notes', 'order_notes'];

    protected $casts = [
        'inventory' => 'boolean'
    ];

    protected $dates = ['deleted_at'];

    protected $fieldNames = [
        'name' => 'Name',
        'notes' => 'Bemerkungen',
        'article_number' => 'Artikelnummer',
        'status' => 'Status',
        'unit_id' => 'Einheit',
        'quantity' => 'Bestand',
        'min_quantity' => 'Mindestbestand',
        'issue_quantity' => 'Entnahmemenge',
        'order_notes' => 'Bestellhinweise',
        'category_id' => 'Kategorie',
        'sort_id' => 'Sortierung',
        'inventory' => 'Inventur',
        'inventory_text' => 'Inventur',
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

    public function scopeWithCurrentSupplierName($query)
    {
        $query->addSubSelect('supplier_name', Supplier::select('name')
            ->whereRaw('current_supplier_id = suppliers.id')
        );
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

    public function scopeWithCurrentSupplierArticle($query)
    {
        $query->addSubSelect('current_supplier_article_id', ArticleSupplier::select('id')
            ->whereRaw('article_id = articles.id')
            ->latest()
        )->with('currentSupplierArticle');
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

    public function changeQuantity($change, $type, $note = '', $deliveryItem = null) {
        $this->quantityChangelogs()->create([
            'user_id' => Auth::id(),
            'type' => $type,
            'change' => $change,
            'new_quantity' => ($this->quantity + $change),
            'note' => $note,
            'delivery_item_id' => optional($deliveryItem)->id,
            'unit_id' => $this->unit_id
        ]);

        $this->quantity = ($this->quantity + $change);
        $this->save();
    }

    public function scopeActive($query) {
        $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeOrderedByName($query) {
        $query->orderBy('name');
    }

    public function scopeOrderedByArticleNumber($query) {
        $query->orderBy('article_number');
    }

    public static function getStatusTextArray() {
        return [
            self::STATUS_ACTIVE => 'aktiv',
            self::STATUS_INACTIVE => 'deaktiviert'
        ];
    }

    public function getShortChangelog() {
        return $this->quantityChangelogs()->with(['user', 'deliveryItem.delivery.order', 'unit'])->latest()->take(30)->get();
    }

    public function openOrders() {
        return $this->belongsToMany(Order::class, 'order_items', 'article_id', 'order_id')->with('items')->statusOpen();
    }

    /**
     * {@inheritdoc}
     */
    public function transformAudit(array $data): array {
        if (Arr::has($data, 'new_values.unit_id')) {
            $data['old_values']['unit_id'] = optional(Unit::find($this->getOriginal('unit_id')))->name;
            $data['new_values']['unit_id'] = Unit::find($this->getAttribute('unit_id'))->name;
        }

        if (Arr::has($data, 'new_values.category_id')) {
            $data['old_values']['category_id'] = Category::find($this->getOriginal('category_id'))->name;
            $data['new_values']['category_id'] = Category::find($this->getAttribute('category_id'))->name;
        }

        if (Arr::has($data, 'new_values.status')) {
            $data['old_values']['status'] = Article::getStatusTextArray()[$this->getOriginal('status')];
            $data['new_values']['status'] = Article::getStatusTextArray()[$this->getAttribute('status')];
        }

        if (Arr::has($data, 'new_values.inventory')) {
            unset($data['old_values']['inventory']);
            unset($data['new_values']['inventory']);
            $data['old_values']['inventory_text'] = $this->getOriginal('inventory') ? 'Ja' : 'Nein';
            $data['new_values']['inventory_text'] = $this->getAttribute('inventory') ? 'Ja' : 'Nein';
        }

        return $data;
    }

    public function getLatestReceipt() {
        return $this->quantityChangelogs()->where('type', ArticleQuantityChangelog::TYPE_INCOMING)->latest()->first();
    }

    public function scopeWithAverageUsage($query)
    {
        $query->addSubSelect('average_usage', ArticleQuantityChangelog::select(DB::raw('AVG(`change`)'))
            ->whereRaw('articles.id = article_quantity_changelogs.article_id')
            ->whereIn('type', [ArticleQuantityChangelog::TYPE_INCOMING, ArticleQuantityChangelog::TYPE_CORRECTION])
            ->where('change', '>', 0)
            ->where('created_at', '>', Carbon::now()->subYear())
            ->groupBy(DB::raw('MONTH(created_at)'))
        );
    }

    public function scopeWithLastReceipt($query)
    {
        $query->addSubSelect('last_receipt', ArticleQuantityChangelog::select('created_at')
            ->whereRaw('articles.id = article_quantity_changelogs.article_id')
            ->where('type', ArticleQuantityChangelog::TYPE_INCOMING)
            ->latest()
        );
    }
}
