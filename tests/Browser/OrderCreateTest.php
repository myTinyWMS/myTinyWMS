<?php


use Carbon\Carbon;
use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Browser;
use Mss\Models\Article;
use Mss\Models\Order;
use Mss\Models\Supplier;
use Tests\DuskTestCase;

class OrderCreateTest extends DuskTestCase
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

    public function test_selecting_supplier_enables_article_adding() {
        $this->browse(function (Browser $browser) {
            $supplier = Supplier::inRandomOrder()->first();

            $browser
                ->visit('/order/create')
                ->assertSee('Bitte zuerst einen Lieferanten auswählen!')
                ->assertDontSee('Artikel hinzufügen')
            ;

            $browser
                ->select('supplier', $supplier->id)
                ->assertDontSee('Bitte zuerst einen Lieferanten auswählen!')
                ->assertSee('Artikel hinzufügen')
            ;
        });
    }

    public function test_selecting_article_list_show_only_articles_from_current_supplier() {
        $this->browse(function (Browser $browser) {
            $supplier = Supplier::inRandomOrder()->first();
            $articles = $supplier->articles()->active()->get();

            $browser
                ->visit('/order/create')
                ->select('supplier', $supplier->id)
                ->click('#add-article')
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->assertSee('Artikel auswählen')
                ->assertSee('1 bis '.$articles->count().' von '.$articles->count().' Einträgen')
            ;
            foreach($articles as $article) {
                $browser->assertPresent('#article_'.$article->id);
            }

        });
    }

    public function test_selecting_article() {
        $this->browse(function (Browser $browser) {
            $supplier = Supplier::inRandomOrder()->first();
            $articles = $supplier->articles()->active()->orderBy('internal_article_number', 'ASC')->get();
            $article = $articles->first();
            $supplierArticle = $article->getCurrentSupplierArticle();
            $supplierArticle->order_quantity = 5;
            $supplierArticle->price = 567;
            $supplierArticle->delivery_time = 5;
            $supplierArticle->save();

            $browser
                ->visit('/order/create')
                ->select('supplier', $supplier->id)
                ->click('#add-article')
                ->waitUntilMissing('#dataTableBuilder_processing')
            ;

            $browser->driver->findElements(WebDriverBy::xpath('//table[@id="dataTableBuilder"]/tbody/tr[2]/td[13]/button'))[0]->click();
            $browser
                ->waitUntilMissing('.v--modal-box')
                ->assertDontSee('Artikel auswählen')
                ->assertSeeIn('.order-article:nth-child(1) .article-name', $article->name)
                ->assertValue('.order-article:nth-child(1) .quantity-select', $supplierArticle->order_quantity)
                ->assertValue('.order-article:nth-child(1) .price-select', number_format($supplierArticle->price / 100, 2, ',', '.'))
                ->assertValue('.order-article:nth-child(1) input[name="expected_delivery[]"]', Carbon::now()->addWeekdays($supplierArticle->delivery_time)->format('Y-m-d'))
            ;
        });
    }

    public function test_adding_two_articles_and_removing_one() {
        $this->browse(function (Browser $browser) {
            $supplier = Supplier::has('articles', '>=', 3)->inRandomOrder()->first();

            $browser
                ->visit('/order/create')
                ->select('supplier', $supplier->id)
                ->click('#add-article')
                ->waitUntilMissing('#dataTableBuilder_processing')
            ;

            // add article 1
            $browser->driver->findElements(WebDriverBy::xpath('//table[@id="dataTableBuilder"]/tbody/tr[2]/td[13]/button'))[0]->click();
            $browser->waitUntilMissing('.v--modal-overlay');

            // add article 2
            $browser->click('#add-article');
            $browser->waitUntilMissing('#dataTableBuilder_processing');
            $browser->driver->findElements(WebDriverBy::xpath('//table[@id="dataTableBuilder"]/tbody/tr[2]/td[13]/button'))[0]->click();
            $browser->waitUntilMissing('.v--modal-overlay');

            $this->assertEquals(2, count($browser->elements('.order-article')));

            $browser->elements('.article-menu')[1]->click();
            $browser->assertSee('ARTIKEL LÖSCHEN');
            $browser->elements('.delete-article')[0]->click();

            $this->assertEquals(1, count($browser->elements('.order-article')));
        });
    }

    public function test_add_two_articles_check_finished_order() {
        $this->browse(function (Browser $browser) {
            $supplier = Supplier::has('articles', '>=', 3)->inRandomOrder()->first();

            $browser
                ->visit('/order/create')
                ->select('supplier', $supplier->id)
                ->click('#add-article')
                ->waitUntilMissing('#dataTableBuilder_processing')
            ;

            $orderNumber = $browser->text('.order-number');

            // add article 1
            $browser->driver->findElements(WebDriverBy::xpath('//table[@id="dataTableBuilder"]/tbody/tr[2]/td[13]/button'))[0]->click();
            $browser->waitUntilMissing('.v--modal-overlay');

            // add article 2
            $browser->click('#add-article');
            $browser->waitUntilMissing('#dataTableBuilder_processing');
            $browser->driver->findElements(WebDriverBy::xpath('//table[@id="dataTableBuilder"]/tbody/tr[2]/td[13]/button'))[0]->click();
            $browser->waitUntilMissing('.v--modal-overlay');
            foreach($browser->elements('.quantity-select') as $element) {
                $element->sendKeys(1);
            }

            $browser
                ->click('#save-order')
                ->waitForText('Bestellung gespeichert');

            $order = Order::where('internal_order_number', $orderNumber)->first();

            $this->assertNotNull($order);
            $this->assertEquals($supplier->id, $order->supplier_id);
            $this->assertEquals(2, $order->items->count());
        });
    }

    public function test_creating_order_from_article() {
        $this->browse(function (Browser $browser) {
            $article = Article::withCurrentSupplier()->active()->inRandomOrder()->first();
            $supplierArticle = $article->getCurrentSupplierArticle();
            $supplierArticle->delivery_time = 3;
            $supplierArticle->save();

            $browser
                ->visit('/order/create?article=' . $article->id)
                ->assertSelected('#supplier', $article->currentSupplier->id)
                ->assertSee($article->name)
                ->assertSeeIn('.order-article:nth-child(1)', $article->name)
                ->assertValue('.order-article:nth-child(1) .quantity-select', $supplierArticle->order_quantity)
                ->assertValue('.order-article:nth-child(1) .price-select', number_format($supplierArticle->price / 100, 2, ',', '.'))
                ->assertValue('.order-article:nth-child(1) input[name="expected_delivery[]"]', Carbon::now()->addWeekdays($supplierArticle->delivery_time)->format('Y-m-d'));
        });
    }
}
