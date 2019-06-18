<?php

namespace Tests\Browser;

use Mss\Models\Article;
use Mss\Models\Category;
use Mss\Models\Supplier;
use Mss\Models\Unit;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ArticleListTest extends DuskTestCase
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

    public function test_create_new_article() {
        $this->browse(function (Browser $browser) {
            $faker = \Faker\Factory::create();
            $category = Category::inRandomOrder()->first();
            $unit = Unit::inRandomOrder()->first();
            $supplier = Supplier::inRandomOrder()->first();

            $name = implode(' ' , $faker->words(3)).' '.$faker->randomNumber(5);
            $tag1 = $faker->word;
            $tag2 = $faker->word;
            $tag3 = $faker->word;
            $notes = $faker->sentence;
            $order_notes = $faker->sentence;
            $supplier_order_number = $faker->randomNumber(6);
            $price = $faker->randomFloat(2, 10, 100);

            $browser
                ->visit('/article/create')
                ->assertSee('Neuer Artikel')
                ->type('#name', $name)
                ->select('#status', Article::STATUS_NO_ORDERS)
//                ->type('#tags', $tag1)
//                ->type('#tags', $tag2)
//                ->type('#tags', $tag3)
                ->select('#category', $category->id)
                ->select('#unit_id', $unit->id)
                ->type('#sort_id', 5)
                ->type('#min_quantity', 10)
                ->type('#issue_quantity', 2)
                ->select('#inventory', Article::INVENTORY_TYPE_CONSUMABLES)
                ->type('#free_lines_in_printed_list', 2)
                ->type('#cost_center', 101)
                ->type('#weight', 10)
                ->select('#packaging_category', Article::PACKAGING_CATEGORY_PAPER)
                ->type('#notes', $notes)
                ->type('#order_notes', $order_notes)

                ->select('#supplier_id', $supplier->id)
                ->type('#supplier_order_number', $supplier_order_number)
                ->type('#supplier_price', $price)
                ->type('#supplier_delivery_time', 1)
                ->type('#supplier_order_quantity', 2)

                ->click('#submit')

                ->waitForText('Artikel angelegt');

            $article = Article::where('name', $name)->withCurrentSupplierArticle()->first();
            $this->assertInstanceOf(Article::class, $article);

            $this->assertEquals(Article::STATUS_NO_ORDERS, $article->status);
            $this->assertEquals($category->id, $article->category_id);
            $this->assertEquals($unit->id, $article->unit_id);
            $this->assertEquals(5, $article->sort_id);
            $this->assertEquals(10, $article->min_quantity);
            $this->assertEquals(2, $article->issue_quantity);
            $this->assertEquals(Article::INVENTORY_TYPE_CONSUMABLES, $article->inventory);
            $this->assertEquals(2, $article->free_lines_in_printed_list);
            $this->assertEquals(101, $article->cost_center);
            $this->assertEquals(10, $article->weight);
            $this->assertEquals(Article::PACKAGING_CATEGORY_PAPER, $article->packaging_category);
            $this->assertEquals($notes, $article->notes);
            $this->assertEquals($order_notes, $article->order_notes);

            $this->assertEquals($supplier->id, $article->currentSupplierArticle->supplier_id);
            $this->assertEquals($supplier_order_number, $article->currentSupplierArticle->order_number);
            $this->assertEquals($price, $article->currentSupplierArticle->price / 100);
            $this->assertEquals(1, $article->currentSupplierArticle->delivery_time);
            $this->assertEquals(2, $article->currentSupplierArticle->order_quantity);
        });
    }
}