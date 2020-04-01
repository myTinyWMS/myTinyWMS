<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Model;

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
