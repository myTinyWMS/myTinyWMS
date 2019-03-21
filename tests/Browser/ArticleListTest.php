<?php

namespace Tests\Browser;

use Mss\Models\Article;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ArticleListTest extends DuskTestCase
{
    /**
     * login before all other tests
     *
     * @throws \Throwable
     */
    public function test_login()
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);
        });
    }

    public function test_article_lists_shows_first_5_active_articles_from_db()
    {
        $articles = Article::active()->orderBy('article_number')->take(5)->get();

        $this->browse(function (Browser $browser) use ($articles) {
            $browser
                ->visit('/article')
                ->assertSee('Artikelübersicht')
                ->waitForLink('Details');

            $articles->each(function ($article) use ($browser) {
                $browser->assertSee($article->name);
            });
        });
    }

    public function test_article_list_shows_disabled_articles() {
        $articles = Article::active()->orderByDesc('article_number')->take(5)->get();
        $articles->each(function ($article) {
            $article->status = Article::STATUS_INACTIVE;
            $article->save();
        });

        $this->browse(function (Browser $browser) use ($articles) {
            $browser
                ->visit('/article')
                ->assertSee('Artikelübersicht')
                ->waitForLink('Details')
                ->select('#filterStatus', '0')
                ->pause(1000)
                ->waitForLink('Details');

            $articles->each(function ($article) use ($browser) {
                $browser->assertSee($article->name);
            });
        });
    }

    public function test_article_list_shows_articles_with_order_stop() {
        $articles = Article::active()->orderByDesc('article_number')->take(5)->get();
        $articles->each(function ($article) {
            $article->status = Article::STATUS_NO_ORDERS;
            $article->save();
        });

        $this->browse(function (Browser $browser) use ($articles) {
            $browser
                ->visit('/article')
                ->assertSee('Artikelübersicht')
                ->waitForLink('Details')
                ->select('#filterStatus', '2')
                ->pause(1000)
                ->waitForLink('Details');

            $articles->each(function ($article) use ($browser) {
                $browser->assertSee($article->name);
            });
        });
    }

    public function test_article_list_search() {
        $article = Article::active()->orderByDesc('article_number')->first();

        $this->browse(function (Browser $browser) use ($article) {
            $browser
                ->visit('/article')
                ->assertSee('Artikelübersicht')
                ->waitForLink('Details')
                ->assertDontSee($article->name)
                ->type('.dataTables_filter input', $article->name)
                ->keys('.dataTables_filter input', $article->name, ['{ENTER}'])
                ->waitForText($article->name, 10);
        });
    }

    public function test_article_list_filter_category() {
        $article = Article::active()->orderByDesc('article_number')->first();

        $this->browse(function (Browser $browser) use ($article) {
            $browser
                ->visit('/article')
                ->assertSee('Artikelübersicht')
                ->waitForLink('Details')
                ->assertDontSee($article->name)
                ->select('#filterCategory', $article->category_id)
                ->waitForText($article->name, 10);
        });
    }

    public function test_article_list_filter_supplier() {
        $article = Article::active()->withCurrentSupplier()->orderByDesc('article_number')->first();

        $this->browse(function (Browser $browser) use ($article) {
            $browser
                ->visit('/article')
                ->assertSee('Artikelübersicht')
                ->waitForLink('Details')
                ->assertDontSee($article->name)
                ->select('#filterSupplier', $article->currentSupplier->id)
                ->waitForText($article->name, 10);
        });
    }
}
