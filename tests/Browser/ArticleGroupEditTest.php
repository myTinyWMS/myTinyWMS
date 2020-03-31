<?php

namespace Tests\Browser;

use Mss\Models\Article;
use Mss\Models\ArticleGroup;
use Mss\Models\ArticleGroupItem;
use Mss\Models\Category;
use Mss\Models\Supplier;
use Mss\Models\Unit;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ArticleGroupEditTest extends DuskTestCase
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

    public function test_article_group_show_view_is_correct() {
        $this->browse(function (Browser $browser) {
            /** @var ArticleGroup $articleGroup */
            $articleGroup = factory(ArticleGroup::class)->create();
            $articleGroup->items()->createMany(
                factory(ArticleGroupItem::class, 3)->make()->toArray()
            );

            $browser
                ->visit('/article-group/'.$articleGroup->id)
                ->assertSee('Details')
                ->assertSee($articleGroup->items[0]->article->name)
                ->assertSee($articleGroup->items[1]->article->name)
                ->assertSee($articleGroup->items[2]->article->name)
                ->assertSee($articleGroup->items[0]->article->article_number)
                ->assertSee($articleGroup->items[1]->article->article_number)
                ->assertSee($articleGroup->items[2]->article->article_number)
                ->assertSeeIn('#group_quantity_'.$articleGroup->items[0]->id, $articleGroup->items[0]->quantity)
                ->assertSeeIn('#group_quantity_'.$articleGroup->items[1]->id, $articleGroup->items[1]->quantity)
                ->assertSeeIn('#group_quantity_'.$articleGroup->items[2]->id, $articleGroup->items[2]->quantity)
                ->assertSeeIn('#current_quantity_'.$articleGroup->items[0]->id, $articleGroup->items[0]->article->quantity)
                ->assertSeeIn('#current_quantity_'.$articleGroup->items[1]->id, $articleGroup->items[1]->article->quantity)
                ->assertSeeIn('#current_quantity_'.$articleGroup->items[2]->id, $articleGroup->items[2]->article->quantity);
        });
    }

    public function test_article_group_edit_form_shown_correctly() {
        $this->browse(function (Browser $browser) {
            $faker = \Faker\Factory::create();

            $articleGroup = factory(ArticleGroup::class)->create();
            $articleGroup->items()->createMany(
                factory(ArticleGroupItem::class, 3)->make()->toArray()
            );

            $newName = implode(' ' , $faker->words(3)).' '.$faker->randomNumber(5);
            $newArticle = Article::whereNotIn('id', $articleGroup->items->pluck('article_id'))->inRandomOrder()->first();
            $newArticle->update(['quantity' => 10]);
            $newQuantity = $faker->numberBetween(1, 5);

            $oldQuantityArticle1 = $articleGroup->items[0]->quantity;
            $oldQuantityArticle2 = $articleGroup->items[1]->quantity;

            $browser
                ->visit('/article-group/'.$articleGroup->id)
                ->assertSee('Details')
                ->click('#edit-group-menu > div > i')
                ->waitForText('Artikelgruppe bearbeiten')
                ->click('#edit-group')
                ->type('#name', $newName)
                // add new article
                ->click('#add-article')
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->type('.dataTables_filter input', $newArticle->article_number)
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->press('auswählen')
                ->waitUntilMissingText('Artikel auswählen')
                ->assertSee($newArticle->name)
                ->assertSee($articleGroup->items[0]->article->name)
                ->assertSee($articleGroup->items[1]->article->name)
                ->assertSee($articleGroup->items[2]->article->name)
                ->type('#quantity_0', $articleGroup->items[0]->quantity + 1)
                ->type('#quantity_1', $articleGroup->items[1]->quantity + 2)
                ->type('#quantity_3', $newQuantity)
                ->click('#submit')
                ->waitForText('Artikelgruppe gespeichert');

            $articleGroup->refresh();
            $this->assertEquals(4, $articleGroup->items->count());
            $this->assertEquals($oldQuantityArticle1 + 1, $articleGroup->items[0]->quantity);
            $this->assertEquals($oldQuantityArticle2 + 2, $articleGroup->items[1]->quantity);
            $this->assertEquals($newArticle->id, $articleGroup->items[3]->article->id);
            $this->assertEquals($newQuantity, $articleGroup->items[3]->quantity);
        });
    }
}