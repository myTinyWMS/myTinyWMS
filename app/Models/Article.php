<?php

namespace Mss\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
    const STATUS_NO_ORDERS = 2;

    protected $auditsToDisplay = 50;

    const INVENTORY_TYPE_SPARE_PARTS = 0;
    const INVENTORY_TYPE_CONSUMABLES = 1;

    protected $fillable = ['name', 'article_number', 'unit_id', 'category_id', 'status', 'quantity', 'min_quantity', 'usage_quantity', 'issue_quantity', 'sort_id', 'inventory', 'notes', 'order_notes'];

    protected $casts = [
        'inventory' => 'boolean',
        'files' => 'array'
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
        'inventory' => 'Inventurtyp',
        'inventory_text' => 'Inventurtyp',
        'files' => 'Dateien',
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

    public function supplierArticles() {
        return $this->hasMany(ArticleSupplier::class);
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

    public function getCurrentSupplierArticle() {
        return Article::where('id', $this->id)->withCurrentSupplierArticle()->first()->currentSupplierArticle;
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
        if (!$this->category) {
            return false;
        }

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

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query) {
        return $query->whereIn('status', [self::STATUS_ACTIVE, self::STATUS_NO_ORDERS]);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderedByName($query) {
        return $query->orderBy('name');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderedByArticleNumber($query) {
        return $query->orderBy('article_number');
    }

    /**
     * @return array
     */
    public static function getStatusTextArray() {
        return [
            self::STATUS_ACTIVE => 'aktiv',
            self::STATUS_INACTIVE => 'deaktiviert',
            self::STATUS_NO_ORDERS => 'Bestellstopp'
        ];
    }

    /**
     * @return array
     */
    public static function getInventoryTextArray() {
        return [
            self::INVENTORY_TYPE_SPARE_PARTS => 'Ersatzteile',
            self::INVENTORY_TYPE_CONSUMABLES => 'Verbrauchsartikel'
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getShortChangelog() {
        return $this->quantityChangelogs()->with(['user', 'deliveryItem.delivery.order', 'unit'])->latest()->take(30)->get();
    }

    /**
     * @return mixed
     */
    public function openOrderItems() {
        return $this->belongsToMany(Order::class, 'order_items', 'article_id', 'order_id')->with('items.order.deliveries')->statusOpen();
    }

    public function openOrders() {
        return $this->openOrderItems->filter(function ($order) {
            return $order->items->where('article_id', $this->id)->filter(function ($item) {
                /* @var $item OrderItem */
                return ($item->quantity != $item->getQuantityDelivered());
            })->count() > 0;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function transformAudit(array $data): array {
        if (Arr::has($data, 'new_values.unit_id')) {
            $data['old_values']['unit_id'] = optional(Unit::find($this->getOriginal('unit_id')))->name;
            $data['new_values']['unit_id'] = optional(Unit::find($this->getAttribute('unit_id')))->name;
        }

        if (Arr::has($data, 'new_values.category_id')) {
            $data['old_values']['category_id'] = optional(Category::find($this->getOriginal('category_id')))->name;
            $data['new_values']['category_id'] = Category::find($this->getAttribute('category_id'))->name;
        }

        if (Arr::has($data, 'new_values.status')) {
            $data['old_values']['status'] = (array_key_exists($this->getOriginal('status'), Article::getStatusTextArray())) ? Article::getStatusTextArray()[$this->getOriginal('status')] : null;
            $data['new_values']['status'] = Article::getStatusTextArray()[$this->getAttribute('status')];
        }

        if (Arr::has($data, 'new_values.inventory')) {
            unset($data['old_values']['inventory']);
            unset($data['new_values']['inventory']);
            $data['old_values']['inventory_text'] = !empty($this->getOriginal('inventory')) ? Article::getInventoryTextArray()[$this->getOriginal('inventory')] : null;
            $data['new_values']['inventory_text'] = !empty($this->getAttribute('inventory')) ? Article::getInventoryTextArray()[$this->getAttribute('inventory')] : null;
        }

        return $data;
    }

    protected function getAuditMappings() {
        return [
            'status' => [
                0 => [0, '0', 'deaktiviert'],
                1 => [1, '1', 'aktiv']
            ]
        ];
    }

    public function getLatestReceipt() {
        return $this->quantityChangelogs()->where('type', ArticleQuantityChangelog::TYPE_INCOMING)->latest()->first();
    }

    public function scopeWithAverageUsage($query) {
        $query->addSubSelect('average_usage', ArticleQuantityChangelog::select(DB::raw('AVG(`change`)'))
            ->whereRaw('articles.id = article_quantity_changelogs.article_id')
            ->whereIn('type', [ArticleQuantityChangelog::TYPE_INCOMING, ArticleQuantityChangelog::TYPE_CORRECTION])
            ->where('change', '>', 0)
            ->where('created_at', '>', Carbon::now()->subYear())
            ->groupBy(DB::raw('MONTH(created_at)'))
        );
    }

    public function scopeWithLastReceipt($query) {
        $query->addSubSelect('last_receipt', ArticleQuantityChangelog::select('created_at')
            ->whereRaw('articles.id = article_quantity_changelogs.article_id')
            ->where('type', ArticleQuantityChangelog::TYPE_INCOMING)
            ->latest()
        );
    }

    public function scopeWithChangelogSumInDateRange($query, Carbon $start, Carbon $end, $type, $fieldname) {
        $type = (!is_array($type)) ? [$type] : $type;
        $query->addSubSelect($fieldname, ArticleQuantityChangelog::select(DB::raw('SUM(`change`)'))
            ->whereRaw('articles.id = article_quantity_changelogs.article_id')
            ->whereBetween('created_at', [$start, $end->copy()->endOfDay()])
            ->whereIn('type', $type)
        );
    }

    public function getAllAudits() {
        $articleSupplierAudits = $this->supplierArticles->transform(function ($item) {
            return $item->getAudits();
        })->flatten(1);

        $audits = $this->getAudits();
        return collect($audits->toArray())->merge($articleSupplierAudits)->sortByDesc('timestamp');
    }

    /**
     * @param $date
     * @return ArticleSupplier|null
     */
    public function getSupplierArticleAtDate($date) {
        $date = ($date instanceof Carbon) ? $date : Carbon::parse($date);

        $supplierArticles = $this->supplierArticles->sortByDesc('created_at');

        // article didn't exists before requested date
        $ignoreArticleCreatedDate = (!empty(env('LAST_ARTICLE_ID_CREATED_ON_FIRST_IMPORT')) && $this->id <= env('LAST_ARTICLE_ID_CREATED_ON_FIRST_IMPORT'));
        if (!$ignoreArticleCreatedDate && $date->lt($this->created_at)) {
            return null;
        }

        // no changes to after requested date, use current value
        if ($supplierArticles->count() === 1) {
            return $supplierArticles->first();
        }

        // search for first change after requested date, use old value
        $firstSupplierArticleAfterDate = $supplierArticles->firstWhere('created_at', '<', $date);
        if ($firstSupplierArticleAfterDate) {
            return $firstSupplierArticleAfterDate;
        }

        Log::error('No SupplierArticle found', compact('supplierArticles'));
        return null;
    }

    /**
     * @param string $attribute
     * @param Carbon|string $date
     * @return mixed|null
     */
    public function getAttributeAtDate($attribute, $date) {
        if ($attribute === 'quantity') {
            return $this->getQuantityAtDate($date);
        } else {
            return parent::getAttributeAtDate($attribute, $date);
        }
    }

    /**
     * @param $query
     * @param Carbon $date
     * @param $fieldname
     */
    public function scopeWithQuantityAtDate($query, $date, $fieldname) {
        $query->addSubSelect($fieldname, ArticleQuantityChangelog::select('new_quantity')
            ->whereRaw('articles.id = article_quantity_changelogs.article_id')
            ->whereIn('type', [ArticleQuantityChangelog::TYPE_START, ArticleQuantityChangelog::TYPE_CORRECTION, ArticleQuantityChangelog::TYPE_INCOMING, ArticleQuantityChangelog::TYPE_INVENTORY, ArticleQuantityChangelog::TYPE_OUTGOING])
            ->where('created_at', '<=', $date->copy()->endOfDay()->format('Y-m-d H:i:s'))
            ->latest()
        );
    }

    /**
     * @param $date
     * @return int|mixed
     */
    public function getQuantityAtDate($date) {
        if (empty($fieldInSubquery)) {
            $fieldInSubquery = 'current_quantity';
            $article = Article::where('id', $this->id)->withQuantityAtDate($date, $fieldInSubquery)->first();
        } else {
            $article = $this;
        }

        if (!is_null($article->{$fieldInSubquery})) {
            return $article->{$fieldInSubquery};
        }

        if ($article->created_at->gt($date)) {
            return 0;
        }

        return $article->quantity;
    }
}
