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
    public function test_login()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1000);
            $this->login($browser);
        });
    }

    /**
     * @return void
     * @throws \Throwable
     */
    public function test_dashboard_shows_article_with_less_quantity()
    {
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
                ->assertSee($article->name);
        });
    }

     /**
     * @return void
     * @throws \Throwable
     */
    public function test_dashboard_does_not_show_disabled_article()
    {
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
                ->assertDontSee($article->name);
        });
    }

     /**
     * @return void
     * @throws \Throwable
     */
    public function test_dashboard_does_not_show_article_with_order_stop()
    {
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
                ->assertDontSee($article->name);
        });
    }

     /**
     * @return void
     * @throws \Throwable
     */
    public function test_dashboard_does_not_show_article_with_min_quantity_of_minus_one()
    {
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
                ->assertDontSee($article->name);
        });
    }

    /**
     * @return void
     * @throws \Throwable
     */
    public function test_dashboard_does_not_show_article_with_current_order()
    {
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
                ->assertDontSee($article->name);
        });
    }
}
