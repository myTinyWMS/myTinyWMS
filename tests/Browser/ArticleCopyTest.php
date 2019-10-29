<?php

namespace Tests\Browser;

use Faker\Factory;
use Mss\Models\Article;
use Mss\Models\ArticleNote;
use Mss\Models\Category;
use Mss\Models\Supplier;
use Mss\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ArticleCopyTest extends DuskTestCase
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

    public function test_copying_article_uses_same_article_attributes() {
        $article = Article::active()->inRandomOrder()->withCurrentSupplierArticle()->first();

        $article->update([
            'name' => 'test article 1',
            'sort_id' => 1,
            'status' => Article::STATUS_NO_ORDERS,
            'min_quantity' => 1,
            'issue_quantity' => 1,
            'inventory' => Article::INVENTORY_TYPE_CONSUMABLES,
            'free_lines_in_printed_list' => 1,
            'packaging_category' => Article::PACKAGING_CATEGORY_PAPER,
            'weight' => 1,
            'cost_center' => 1,
            'notes' => 'foo bar',
            'order_notes' => 'lorem ipsum'
        ]);

        $currentSupplierArticle = $article->currentSupplierArticle;
        $currentSupplierArticle->update([
            'order_number' => 1,
            'price' => 200,
            'delivery_time' => 3,
            'order_quantity' => 4
        ]);

        $this->browse(function (Browser $browser) use ($article, $currentSupplierArticle) {
            $faker = \Faker\Factory::create();
            $newName = implode(' ' , $faker->words(3)).' '.$faker->randomNumber(5);

            $browser
                ->visit('/article/' . $article->id . '/copy')
                ->assertSelected('#status', Article::STATUS_NO_ORDERS)
                ->assertValue('#name', 'test article 1')
                ->assertValue('#sort_id', 1)
                ->assertValue('#min_quantity', 1)
                ->assertValue('#issue_quantity', 1)
                ->assertSelected('#inventory', Article::INVENTORY_TYPE_CONSUMABLES)
                ->assertValue('#free_lines_in_printed_list', 1)
                ->assertSelected('#packaging_category', Article::PACKAGING_CATEGORY_PAPER)
                ->assertValue('#weight', 1)
                ->assertValue('#cost_center', 1)
                ->assertValue('#notes', 'foo bar')
                ->assertValue('#order_notes', 'lorem ipsum')

                ->assertSelected('#supplier_id', $currentSupplierArticle->supplier_id)
                ->assertValue('#supplier_order_number', 1)
                ->assertValue('#supplier_price', 2)
                ->assertValue('#supplier_delivery_time', 3)
                ->assertValue('#supplier_order_quantity', 4)

                ->type('#name', $newName)
                ->click('#submit')
                ->waitForText('Artikel angelegt')
            ;

            $article = Article::where('name', $newName)->withCurrentSupplierArticle()->first();
            $this->assertInstanceOf(Article::class, $article);
        });
    }
}