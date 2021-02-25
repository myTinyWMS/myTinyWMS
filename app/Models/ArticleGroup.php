<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class ArticleGroup
 *
 * @property integer id
 * @property string name
 * @property string external_article_number
 * @property ArticleGroupItem[]|Collection items
 * @method static ArticleGroup first()
 * @package Mss\Models
 */
class ArticleGroup extends AuditableModel
{
    protected $fillable = ['name', 'external_article_number'];

    public function items() {
        return $this->hasMany(ArticleGroupItem::class);
    }

    /**
     * @inheritDoc
     */
    public static function getAuditName()
    {
        return __('Artikelgruppe');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldNames()
    {
        return [
            'name' => __('Name')
        ];
    }

    public function getArticleNumber() {
        return sprintf("AG%08s\n", $this->id);
    }
}
