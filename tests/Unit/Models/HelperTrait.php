<?php

namespace Tests\Unit\Models;

use Mss\Models\ArticleQuantityChangelog;
use OwenIt\Auditing\Models\Audit;

trait HelperTrait {
    /**
     * @param $createdAt
     * @param $article
     * @param $newQuantity
     * @param $type
     * @param $change
     */
    protected function createArticleChangelog($createdAt, &$article, $newQuantity, $type, $change) {
        ArticleQuantityChangelog::create([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
            'article_id' => $article->id,
            'new_quantity' => $newQuantity,
            'user_id' => 1,
            'type' => $type,
            'change' => $change
        ]);

        $this->createArticleAudit($article, ['quantity' => $article->quantity], ['quantity' => $newQuantity], $createdAt);
        $article->quantity = $newQuantity;
    }

    /**
     * @param $article
     * @param $oldValues
     * @param $newValues
     * @param $createdAt
     */
    protected function createArticleAudit($article, $oldValues, $newValues, $createdAt) {
        Audit::create([
            'user_id' => 1,
            'event' => 'updated',
            'auditable_type' => 'article',
            'auditable_id' => $article->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'created_at' => $createdAt,
        ]);
    }
}