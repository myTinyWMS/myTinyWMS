<?php

namespace Tests\Unit\Models;

use Carbon\Carbon;
use Mss\Models\ArticleSupplier;
use Mss\Models\Supplier;
use OwenIt\Auditing\Models\Audit;
use Tests\TestCase;
use Mss\Models\Article;
use Tests\Unit\Models\HelperTrait;
use Mss\Models\ArticleQuantityChangelog;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ArticleTest extends TestCase
{
    use HelperTrait;

    public function test_quantity_at_date_before_first_change() {
        /* @var $article Article */
        $article = factory(Article::class)->create([
            'quantity' => 24,
            'created_at' => Carbon::parse('2018-07-23 10:58:00')
        ]);

        $this->createArticleChangelog(Carbon::parse('2018-08-07 15:51:00'), $article, 32, ArticleQuantityChangelog::TYPE_INCOMING, 32);
        $this->createArticleChangelog(Carbon::parse('2018-08-24 08:59:00'), $article, 24, ArticleQuantityChangelog::TYPE_OUTGOING, -8);

        $article->load('audits');

        $start = Carbon::parse('2018-08-01');
        $this->assertEquals(0, $article->getAttributeAtDate('quantity', $start->copy()->subDay()));
    }


    public function test_quantity_at_date_specific_changelog_entry() {
        /* @var $article Article */
        $article = factory(Article::class)->create([
            'quantity' => 20,
            'created_at' => Carbon::now()->subDays(5)
        ]);

        $this->createArticleChangelog(Carbon::now()->subDays(4), $article, 5, ArticleQuantityChangelog::TYPE_INCOMING, 5);
        $this->createArticleChangelog(Carbon::now()->subDays(3), $article, 10, ArticleQuantityChangelog::TYPE_INCOMING, 5);
        $this->createArticleChangelog(Carbon::now()->subDays(2), $article, 15, ArticleQuantityChangelog::TYPE_INCOMING, 5);
        $this->createArticleChangelog(Carbon::now()->subDays(1), $article, 20, ArticleQuantityChangelog::TYPE_INCOMING, 5);

        $article->load('audits');

        $this->assertEquals(5, $article->getAttributeAtDate('quantity', Carbon::now()->subDays(4)));
    }

    public function test_changelog_sum_in_date_range() {
        /* @var $article Article */
        $article = factory(Article::class)->create([
            'quantity' => 25
        ]);

        $this->createArticleChangelog(Carbon::parse('2018-04-30 12:10:00'), $article, 5, ArticleQuantityChangelog::TYPE_INCOMING, 5);
        $this->createArticleChangelog(Carbon::parse('2018-05-01 10:00:00'), $article, 10, ArticleQuantityChangelog::TYPE_INCOMING, 5);
        $this->createArticleChangelog(Carbon::parse('2018-05-10 10:00:00'), $article, 15, ArticleQuantityChangelog::TYPE_INCOMING, 5);
        $this->createArticleChangelog(Carbon::parse('2018-05-31 10:00:00'), $article, 20, ArticleQuantityChangelog::TYPE_INCOMING, 5);
        $this->createArticleChangelog(Carbon::parse('2018-06-01 10:00:00'), $article, 25, ArticleQuantityChangelog::TYPE_INCOMING, 5);

        $start = Carbon::parse('2018-05-01');
        $end = $start->copy()->lastOfMonth();

        $article = Article::where('id', $article->id)->withChangelogSumInDateRange($start, $end, ArticleQuantityChangelog::TYPE_INCOMING, 'total_incoming')->first();
        $this->assertEquals(15, $article->total_incoming);
    }

    public function test_getSupplierArticleAtDate_returns_null_as_requested_date_is_before_created_at() {
        $supplier = factory(Supplier::class)->create(['created_at' => now()->subWeeks(1)]);
        /* @var $article Article */
        $article = factory(Article::class)->create(['created_at' => now()->subWeeks(1)]);
        $supplierArticle = ArticleSupplier::create([
            'created_at' => now()->subWeeks(1),
            'article_id' => $article->id,
            'supplier_id' => $supplier->id,
            'order_number' => '1',
            'price' => 1
        ]);

        $article = Article::where('id', $article->id)->withCurrentSupplierArticle()->first();
        $article->load('supplierArticles');
        $this->assertEquals(null, $article->getSupplierArticleAtDate(now()->subWeeks(2)));
    }

    public function test_getSupplierArticleAtDate_returns_current_value_as_requested_date_is_after_created_at() {
        $supplier = factory(Supplier::class)->create(['created_at' => now()->subWeeks(1)]);
        /* @var $article Article */
        $article = factory(Article::class)->create(['created_at' => now()->subWeeks(1)]);
        $supplierArticle = ArticleSupplier::create([
            'created_at' => now()->subWeeks(1),
            'article_id' => $article->id,
            'supplier_id' => $supplier->id,
            'order_number' => '1',
            'price' => 1
        ]);
        $article = Article::where('id', $article->id)->withCurrentSupplierArticle()->first();
        $article->load('supplierArticles');

        $this->assertTrue($supplierArticle->is($article->getSupplierArticleAtDate(now())));
    }

    public function test_getSupplierArticleAtDate_returns_previous_value_as_requested_date_is_before_last_item() {
        $supplier1 = factory(Supplier::class)->create(['created_at' => now()->subWeeks(4)]);
        $supplier2 = factory(Supplier::class)->create(['created_at' => now()->subWeeks(4)]);
        /* @var $article Article */
        $article = factory(Article::class)->create(['created_at' => now()->subWeeks(4)]);

        $supplierArticle1 = ArticleSupplier::create([
            'created_at' => now()->subWeeks(3),
            'article_id' => $article->id,
            'supplier_id' => $supplier1->id,
            'order_number' => '1',
            'price' => 1
        ]);

        $supplierArticle2 = ArticleSupplier::create([
            'created_at' => now()->subWeeks(1),
            'article_id' => $article->id,
            'supplier_id' => $supplier2->id,
            'order_number' => '1',
            'price' => 1
        ]);

        $article = Article::where('id', $article->id)->withCurrentSupplierArticle()->first();
        $article->load('supplierArticles');

        $this->assertTrue($supplierArticle1->is($article->getSupplierArticleAtDate(now()->subWeeks(2))));
    }

    public function test_old_price_of_supplier_article_returned_after_changing_price_and_supplier_article() {
        $supplier1 = factory(Supplier::class)->create(['created_at' => now()->subWeeks(4)]);
        $supplier2 = factory(Supplier::class)->create(['created_at' => now()->subWeeks(4)]);
        /* @var $article Article */
        $article = factory(Article::class)->create(['created_at' => now()->subWeeks(4)]);

        $supplierArticle1 = ArticleSupplier::create([
            'created_at' => now()->subWeeks(4),
            'article_id' => $article->id,
            'supplier_id' => $supplier1->id,
            'order_number' => '1',
            'price' => 1
        ]);

        $supplierArticle2 = ArticleSupplier::create([
            'created_at' => now()->subWeeks(1),
            'article_id' => $article->id,
            'supplier_id' => $supplier2->id,
            'order_number' => '1',
            'price' => 3
        ]);

        Audit::create([
            'user_id' => 1,
            'user_type' => 'Mss\Models\User',
            'event' => 'updated',
            'auditable_type' => 'Mss\Models\ArticleSupplier',
            'auditable_id' => $supplierArticle1->id,
            'old_values' => ['price' => 2],
            'new_values' => ['price' => 1],
            'created_at' => now()->subWeeks(2),
        ]);

        $article = Article::where('id', $article->id)->withCurrentSupplierArticle()->first();
        $article->load('supplierArticles');

        $this->assertEquals(2, $article->getSupplierArticleAtDate(now()->subWeeks(3))->getAttributeAtDate('price', now()->subWeeks(3)));
        $this->assertEquals(1, $article->getSupplierArticleAtDate(now()->subWeeks(1)->subDay())->getAttributeAtDate('price', now()->subWeeks(1)->subDay()));
        $this->assertEquals(3, $article->getSupplierArticleAtDate(now()->subDays(3))->getAttributeAtDate('price', now()->subDays(3)));
    }
}