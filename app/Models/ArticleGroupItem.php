<?php

namespace Mss\Models;

/**
 * Class ArticleGroupItem
 *
 * @property integer id
 * @property integer article_group_id
 * @property integer article_id
 * @property integer quantity
 * @property Article article
 * @package Mss\Models
 */
class ArticleGroupItem extends AuditableModel
{
    protected $fillable = ['article_id', 'quantity'];

    public function articleGroup() {
        return $this->belongsTo(ArticleGroup::class);
    }

    public function article() {
        return $this->belongsTo(Article::class);
    }

    /**
     * @inheritDoc
     */
    public static function getAuditName()
    {
        return __('Artikelgruppe-Artikel');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldNames()
    {
        return [
            'quantity' => __('Menge')
        ];
    }
}
