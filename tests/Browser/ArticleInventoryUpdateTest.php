<?php

namespace Tests\Browser;

use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;
use Mss\Models\Category;
use Mss\Models\Supplier;
use Mss\Models\Unit;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ArticleInventoryUpdateTest extends DuskTestCase
{
    /**
     * login before all other tests
     *
     * @throws \Throwable
     */
    public function test_login() {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 2000);
            $this->login($browser);
        });
    }

    public function test_article_inventory_update() {
        $this->browse(function (Browser $browser) {
            $articles = Article::active()->where('quantity', '>', 3)->inRandomOrder()->take(5)->get();
            
            $article1 = $articles->get(0);
            $article2 = $articles->get(1);
            $article3 = $articles->get(2);
            $article4 = $articles->get(3);
            $article5 = $articles->get(4);

            $newQuantity1 = $article1->quantity + 5;
            $newQuantity2 = $article2->quantity + 5;
            $newQuantity3 = $article3->quantity + 5;
            $newQuantity4 = $article4->quantity - 2;
            $newQuantity5 = $article5->quantity - 2;

            $browser->visit('/article/inventory-update')
                ->waitForText('Speichern')
                ->type('#quantity_'.$article1->id, $newQuantity1)
                ->type('#quantity_'.$article2->id, $newQuantity2)
                ->type('#quantity_'.$article3->id, $newQuantity3)
                ->type('#quantity_'.$article4->id, $newQuantity4)
                ->type('#quantity_'.$article5->id, $newQuantity5)
                ->click('#submit')
                ->waitForText('Änderungen gespeichert');

            $this->assertTrue(ArticleQuantityChangelog::where([
                'article_id' => $article1->id,
                'type' => ArticleQuantityChangelog::TYPE_INVENTORY,
                'change' => 5,
                'new_quantity' => $newQuantity1,
            ])->exists());

            $this->assertTrue(ArticleQuantityChangelog::where([
                'article_id' => $article2->id,
                'type' => ArticleQuantityChangelog::TYPE_INVENTORY,
                'change' => 5,
                'new_quantity' => $newQuantity2,
            ])->exists());

            $this->assertTrue(ArticleQuantityChangelog::where([
                'article_id' => $article3->id,
                'type' => ArticleQuantityChangelog::TYPE_INVENTORY,
                'change' => 5,
                'new_quantity' => $newQuantity3,
            ])->exists());

            $this->assertTrue(ArticleQuantityChangelog::where([
                'article_id' => $article4->id,
                'type' => ArticleQuantityChangelog::TYPE_INVENTORY,
                'change' => -2,
                'new_quantity' => $newQuantity4,
            ])->exists());

            $this->assertTrue(ArticleQuantityChangelog::where([
                'article_id' => $article5->id,
                'type' => ArticleQuantityChangelog::TYPE_INVENTORY,
                'change' => -2,
                'new_quantity' => $newQuantity5,
            ])->exists());
            
            $article1->refresh();
            $article2->refresh();
            $article3->refresh();
            $article4->refresh();
            $article5->refresh();

            $this->assertEquals($newQuantity1, $article1->quantity);
            $this->assertEquals($newQuantity2, $article2->quantity);
            $this->assertEquals($newQuantity3, $article3->quantity);
            $this->assertEquals($newQuantity4, $article4->quantity);
            $this->assertEquals($newQuantity5, $article5->quantity);
        });
    }
}