<?php

namespace Mss\Models;

class ArticleGroup extends AuditableModel
{
    protected $fillable = ['name'];

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
}
