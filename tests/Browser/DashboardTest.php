<?php

namespace Tests\Browser;

use Mss\Models\Article;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class DashboardTest extends DuskTestCase
{
    /**
     * login before all other tests
     *
     * @throws \Throwable
     */
    public function test_login() {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1000);
            $this->login($browser);
        });
    }

    /**
     * @return void
     * @throws \Throwable
     */
    public function test_dashboard_shows_article_with_less_quantity() {
        $article = factory(Article::class)->create([
            'quantity' => 10,
            'min_quantity' => 20,
            'status' => Article::STATUS_ACTIVE
        ]);

        $this->browse(function (Browser $browser) use ($article) {
            $browser
                ->visit('/')
                ->assertSee('Dashboard')
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->assertSeeIn('#dataTableBuilder tbody ', $article->name);
        });
    }

     /**
     * @return void
     * @throws \Throwable
     */
    public function test_dashboard_does_not_show_disabled_article() {
        $article = factory(Article::class)->create([
            'quantity' => 10,
            'min_quantity' => 20,
            'status' => Article::STATUS_INACTIVE
        ]);

        $this->browse(function (Browser $browser) use ($article) {
            $browser
                ->visit('/')
                ->assertSee('Dashboard')
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->assertDontSeeIn('#dataTableBuilder tbody ', $article->name);
        });
    }

     /**
     * @return void
     * @throws \Throwable
     */
    public function test_dashboard_does_not_show_article_with_order_stop() {
        $article = factory(Article::class)->create([
            'quantity' => 10,
            'min_quantity' => 20,
            'status' => Article::STATUS_NO_ORDERS
        ]);

        $this->browse(function (Browser $browser) use ($article) {
            $browser
                ->visit('/')
                ->assertSee('Dashboard')
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->assertDontSeeIn('#dataTableBuilder tbody ', $article->name);
        });
    }

     /**
     * @return void
     * @throws \Throwable
     */
    public function test_dashboard_does_not_show_article_with_min_quantity_of_minus_one() {
        $article = factory(Article::class)->create([
            'quantity' => 10,
            'min_quantity' => -1,
            'status' => Article::STATUS_ACTIVE
        ]);

        $this->browse(function (Browser $browser) use ($article) {
            $browser
                ->visit('/')
                ->assertSee('Dashboard')
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->assertDontSeeIn('#dataTableBuilder tbody ', $article->name);
        });
    }

    /**
     * @return void
     * @throws \Throwable
     */
    public function test_dashboard_does_not_show_article_with_current_order() {
        $article = factory(Article::class)->create([
            'quantity' => 10,
            'min_quantity' => 20,
            'status' => Article::STATUS_ACTIVE
        ]);

        factory(\Mss\Models\Order::class)->create()->each(function ($order) use ($article) {
            factory(\Mss\Models\OrderItem::class)->create()->each(function ($orderItem) use ($article, $order) {
                $orderItem->article_id = $article->id;
                $orderItem->order()->associate($order);
                $orderItem->save();
            });
        });

        $this->browse(function (Browser $browser) use ($article) {
            $browser
                ->visit('/')
                ->assertSee('Dashboard')
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->assertDontSeeIn('#dataTableBuilder tbody ', $article->name);
        });
    }

    public function test_create_order_from_dashboard() {
        $article = Article::active()->whereNotNull('article_number')->first();
        $article->quantity = 10;
        $article->min_quantity = 20;
        $article->save();

        $this->browse(function (Browser $browser) use ($article) {
            $browser
                ->visit('/')
                ->assertSee('Dashboard')
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->click('#new_order_'.$article->id)
                ->click('#create_new_order')
                ->waitForText('Neue Bestellung')
                ->assertSeeIn('#article-list ', $article->name);
        });
    }
}
