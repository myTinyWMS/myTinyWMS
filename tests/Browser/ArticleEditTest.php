<?php

namespace Tests\Browser;

use Faker\Factory;
use Mss\Models\Article;
use Mss\Models\Category;
use Mss\Models\Supplier;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ArticleEditTest extends DuskTestCase
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

    public function test_increasing_quantity_as_inventory() {
        $article = Article::active()->where('quantity', '>', 10)->inRandomOrder()->first();
        $this->changeQuantity($article, 'add', 2, 5, $article->quantity + 5);
    }

    public function test_decreasing_quantity_as_inventory() {
        $article = Article::active()->where('quantity', '>', 10)->inRandomOrder()->first();
        $this->changeQuantity($article, 'sub', 2, 5, $article->quantity - 5);
    }

    public function test_moving_some_items_to_external_storage_and_back() {
        $article = Article::active()->where('quantity', '>', 10)->where('outsourcing_quantity', 0)->inRandomOrder()->first();

        // move
        $this->changeQuantity($article, 'sub', 4, 5, $article->quantity);
        $article->refresh();
        $this->assertEquals(5, $article->outsourcing_quantity);

        // release
        $this->changeQuantity($article, 'add', 4, 5, $article->quantity);
        $article->refresh();
        $this->assertEquals(0, $article->outsourcing_quantity);
    }

    public function test_changing_basic_article_attributes() {
        $article = Article::active()->inRandomOrder()->first();

        $article->update([
            'name' => 'test article 1',
            'sort_id' => 1,
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

        $article->refresh();

        $this->browse(function (Browser $browser) use ($article) {
            $browser
                ->visit('/article/' . $article->id)
                ->assertSee($article->article_number)
                ->select('#status', Article::STATUS_NO_ORDERS)
                ->type('#name', 'test article 2')
                ->type('#sort_id', 2)
                ->type('#min_quantity', 2)
                ->type('#issue_quantity', 2)
                ->select('#inventory', Article::INVENTORY_TYPE_SPARE_PARTS)
                ->type('#free_lines_in_printed_list', 2)
                ->select('#packaging_category', Article::PACKAGING_CATEGORY_PAPER)
                ->type('#weight', 2)
                ->type('#cost_center', 2)
                ->type('#notes', 'bla blub')
                ->type('#order_notes', 'test test')
                ->click('#saveArticle')
                ->waitForText('Artikel gespeichert')
            ;
        });

        $article->refresh();

        $this->assertEquals(Article::STATUS_NO_ORDERS, $article->status);
        $this->assertEquals('test article 2', $article->name);
        $this->assertEquals(2, $article->sort_id);
        $this->assertEquals(2, $article->min_quantity);
        $this->assertEquals(2, $article->issue_quantity);
        $this->assertEquals(Article::INVENTORY_TYPE_SPARE_PARTS, $article->inventory);
        $this->assertEquals(2, $article->free_lines_in_printed_list);
        $this->assertEquals(Article::PACKAGING_CATEGORY_PAPER, $article->packaging_category);
        $this->assertEquals(2, $article->weight);
        $this->assertEquals(2, $article->cost_center);
        $this->assertEquals('bla blub', $article->notes);
        $this->assertEquals('test test', $article->order_notes);
    }

    public function test_changing_category_of_article() {
        $article = Article::active()->inRandomOrder()->first();
        $oldCategory = $article->category;
        $newCategory = Category::where('id', '!=', $oldCategory->id)->inRandomOrder()->first();

        $oldArticleNumber = $article->article_number;

        $this->browse(function (Browser $browser) use ($article, $newCategory, $oldArticleNumber) {
            $browser
                ->visit('/article/' . $article->id)
                ->assertSee($article->article_number)
                ->click('#enableChangeCategory ~ ins')
                ->select('#category', $newCategory->id)
                ->click('#saveArticle')
                ->waitForText('Artikel gespeichert')
                ->assertDontSee($oldArticleNumber)
            ;

            $article->refresh();
            $browser->assertSee($article->article_number);
            $this->assertNotEquals($oldArticleNumber, $article->article_number);
        });
    }

    public function test_changing_supplier_details() {
        $article = Article::active()->withCurrentSupplierArticle()->inRandomOrder()->first();
        $currentSupplierArticle = $article->currentSupplierArticle;
        $currentSupplierArticle->update([
            'order_number' => 1,
            'price' => 1,
            'delivery_time' => 1,
            'order_quantity' => 1
        ]);

        $this->browse(function (Browser $browser) use ($article, $currentSupplierArticle) {
            $browser
                ->visit('/article/' . $article->id)
                ->assertSee($article->article_number)
                ->click('#changeSupplierMenu')
                ->click('#changeSupplierLink')
                ->waitForText('Lieferant bearbeiten')
                ->type('#order_number', 2)
                ->type('#price', 2)
                ->type('#delivery_time', 2)
                ->type('#order_quantity', 2)
                ->click('#saveChangeSupplier')
                ->waitForText('Lieferantendaten gespeichert')
            ;

            $currentSupplierArticle->refresh();

            $this->assertEquals(2, $currentSupplierArticle->order_number);
            $this->assertEquals(200, $currentSupplierArticle->price);
            $this->assertEquals(2, $currentSupplierArticle->delivery_time);
            $this->assertEquals(2, $currentSupplierArticle->order_quantity);
        });
    }

    public function test_change_supplier() {
        $article = Article::active()->withCurrentSupplier()->inRandomOrder()->first();
        $newSupplier = Supplier::where('id', '!=', $article->currentSupplier->id)->inRandomOrder()->first();

        $this->browse(function (Browser $browser) use ($article, $newSupplier) {
            $browser
                ->visit('/article/' . $article->id)
                ->assertSee($article->article_number)
                ->click('#changeSupplierMenu')
                ->click('#changeSupplierLink')
                ->waitForText('Lieferant bearbeiten')
                ->select('#supplier', $newSupplier->id)
                ->click('#saveChangeSupplier')
                ->waitForText('Lieferantendaten gespeichert')
            ;

            $article = Article::withCurrentSupplier()->where('id', $article->id)->first();
            $this->assertEquals($newSupplier->id, $article->currentSupplier->id);
        });
    }

    public function test_uploading_file_to_article() {
        $article = Article::active()->whereNull('files')->inRandomOrder()->first();

        $this->browse(function (Browser $browser) use ($article) {
            $browser
                ->visit('/article/' . $article->id)
                ->assertSee($article->article_number)
                ->attach('input.dz-hidden-input', '/data/www/tests/Browser/test.png')
                ->waitFor('.dz-success-mark')
                ->refresh()
                ->assertSeeIn('#fileList', 'test.png');
            ;

            $article->refresh();

            $attachment = $article->files[0];
            $this->assertFileExists(storage_path('app/article_files/'.$attachment['storageName']));
            unlink(storage_path('app/article_files/'.$attachment['storageName']));
        });
    }

    public function test_adding_notes_to_article() {
        $article = Article::active()->inRandomOrder()->first();

        $faker = Factory::create();
        $note = $faker->sentence;

        $this->browse(function (Browser $browser) use ($article, $note) {
            $browser
                ->visit('/article/' . $article->id)
                ->assertSee($article->article_number)
                ->click('#addNote')
                ->type('#new_note', $note)
                ->click('#addNoteSubmit')
                ->waitForText($note)
            ;
        });
    }

    /**
     * @param Article $article
     * @param $changeType
     * @param $changelogTypeIndex
     * @param $changeQuantity
     * @param $expectedQuantity
     * @throws \Throwable
     */
    protected function changeQuantity($article, $changeType, $changelogTypeIndex, $changeQuantity, $expectedQuantity) {
        $faker = Factory::create();
        $note = $faker->sentence;

        $this->browse(function (Browser $browser) use ($article, $note, $changeType, $changelogTypeIndex, $changeQuantity, $expectedQuantity) {
            $browser
                ->visit('/article/'.$article->id)
                ->assertSee($article->article_number)
                ->click('.edit-quantity')
                ->select('#changelogChangeType', $changeType)
                ->type('changelogChange', $changeQuantity)
                ->click('#changelogType')
                ->elements('#changelogType option')[$changelogTypeIndex]->click();

            $browser
                ->type('#changelogNote', $note)
                ->click('#submitChangeQuantity');

            $browser->driver->switchTo()->alert()->accept();

            $browser
                ->waitForText('Bestand geändert')
                ->assertSeeIn('#currentQuantity', $expectedQuantity)
                ->assertSeeIn('#articleQuantityChangelogTable tbody', $note)
            ;

            $article->refresh();
            $this->assertEquals($expectedQuantity, $article->quantity);
        });
    }
}