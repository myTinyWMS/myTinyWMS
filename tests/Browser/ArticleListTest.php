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
            $browser->resize(1920, 2000);
            $this->login($browser);
        });
    }

    public function test_article_lists_shows_first_5_active_articles_from_db()
    {
        $articles = Article::enabled()->orderBy('article_number')->take(5)->get();

        $this->browse(function (Browser $browser) use ($articles) {
            $browser
                ->visit('/article')
                ->assertSee('Artikelübersicht')
                ->waitUntilMissing('#dataTableBuilder_processing');

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
                ->click('#table-filter')
                ->select('#filterStatus', '0')
                ->waitUntilMissing('#dataTableBuilder_processing');

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
                ->click('#table-filter')
                ->select('#filterStatus', '2')
                ->waitUntilMissing('#dataTableBuilder_processing');

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
                ->assertDontSee($article->article_number)
                ->type('.dataTables_filter input', $article->name)
                ->waitUntilMissingText('Bitte warten')
                ->pause(1000)
                ->assertSee($article->article_number);
        });
    }

    public function test_article_list_filter_category() {
        $article = Article::active()->orderByDesc('article_number')->first();

        $this->browse(function (Browser $browser) use ($article) {
            $browser
                ->visit('/article')
                ->assertSee('Artikelübersicht')
                ->waitForLink('Details')
                ->assertDontSee($article->article_number)
                ->click('#table-filter')
                ->select('#filterCategory', $article->category_id)
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->assertSee($article->article_number);
        });
    }

    public function test_article_list_filter_supplier() {
        $article = Article::active()->withCurrentSupplier()->orderByDesc('article_number')->first();

        $this->browse(function (Browser $browser) use ($article) {
            $browser
                ->visit('/article')
                ->assertSee('Artikelübersicht')
                ->waitForLink('Details')
                ->assertDontSee($article->article_number)
                ->click('#table-filter')
                ->select('#filterSupplier', $article->currentSupplier->id)
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->assertSee($article->article_number);
        });
    }
}