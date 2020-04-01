<?php

namespace Tests\Browser;

use Mss\Models\Article;
use Mss\Models\ArticleGroup;
use Mss\Models\Category;
use Mss\Models\Supplier;
use Mss\Models\Unit;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ArticleGroupCreateTest extends DuskTestCase
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

    public function test_create_new_article_group() {
        $this->browse(function (Browser $browser) {
            $faker = \Faker\Factory::create();

            $name = implode(' ' , $faker->words(3)).' '.$faker->randomNumber(5);

            $article1 = Article::inRandomOrder()->first();
            $article1->update(['quantity' => 10]);
            $quantity1 = $faker->numberBetween(1, 5);

            $article2 = Article::where('id', '!=', $article1->id)->inRandomOrder()->first();
            $article2->update(['quantity' => 15]);
            $quantity2 = $faker->numberBetween(1, 5);

            $browser
                ->visit('/article-group/create')
                ->assertSee('Neue Artikelgruppe')
                ->type('#name', $name)
                // add article 1
                ->click('#add-article')
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->type('.dataTables_filter input', $article1->article_number)
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->press('auswählen')
                ->waitUntilMissingText('Artikel auswählen')
                ->assertSee($article1->name)
                // add article 2
                ->click('#add-article')
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->type('.dataTables_filter input', $article2->article_number)
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->press('auswählen')
                ->waitUntilMissingText('Artikel auswählen')
                ->assertSee($article2->name)
                ->assertSee($article1->name)    // should still be visible
                ->type('#quantity_0', $quantity1)
                ->type('#quantity_1', $quantity2)
                ->click('#submit')
                ->waitForText('Artikelgruppe gespeichert');

            $articleGroup = ArticleGroup::where('name', $name)->with('items.article')->first();

            $this->assertInstanceOf(ArticleGroup::class, $articleGroup);
            $this->assertEquals(2, $articleGroup->items->count());
            $this->assertEquals($quantity1, $articleGroup->items[0]->quantity);
            $this->assertEquals($quantity2, $articleGroup->items[1]->quantity);
            $this->assertEquals($article1->id, $articleGroup->items[0]->article->id);
            $this->assertEquals($article2->id, $articleGroup->items[1]->article->id);
        });
    }
}