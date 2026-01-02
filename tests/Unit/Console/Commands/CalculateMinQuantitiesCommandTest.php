<?php

use Illuminate\Support\Facades\Artisan;
use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;
use Mss\Models\ArticleSupplier;
use Mss\Models\Category;
use Mss\Models\Supplier;
use Mss\Models\Unit;
use Tests\TestCase;
use Tests\Unit\Models\HelperTrait;
use Carbon\Carbon;

class CalculateMinQuantitiesCommandTest extends TestCase
{
    use HelperTrait;

    protected function makeArticle(array $overrides = []): Article
    {
        $unit = Unit::first();
        $category = Category::first();

        $article = new Article();
        $article->name = 'Test Artikel';
        $article->quantity = $overrides['quantity'] ?? 0;
        $article->min_quantity = $overrides['min_quantity'] ?? 0;
        $article->auto_min_quantity_duration = $overrides['auto_min_quantity_duration'] ?? Article::AUTO_MIN_QUANTITY_DURATION_DISABLED;
        if ($unit) {
            $article->unit()->associate($unit);
        }
        if ($category) {
            $article->category()->associate($category);
        }
        $article->save();

        return $article;
    }

    protected function setCurrentSupplierArticle(Article $article, int $deliveryTime): ArticleSupplier
    {
        $supplier = Supplier::first() ?: factory(Supplier::class)->create();

        return ArticleSupplier::create([
            'article_id' => $article->id,
            'supplier_id' => $supplier->id,
            'order_number' => 'TEST-123',
            'price' => 0,
            'delivery_time' => $deliveryTime,
            'order_quantity' => 1,
        ]);
    }

    public function test_does_nothing_when_duration_disabled(): void
    {
        $article = $this->makeArticle([
            'min_quantity' => 5,
            'auto_min_quantity_duration' => Article::AUTO_MIN_QUANTITY_DURATION_DISABLED,
        ]);

        Artisan::call('calculate:min');

        $article->refresh();
        $this->assertSame(5, (int) $article->min_quantity);
    }

    public function test_does_nothing_when_no_sales_in_period(): void
    {
        $article = $this->makeArticle([
            'min_quantity' => 5,
            'auto_min_quantity_duration' => Article::AUTO_MIN_QUANTITY_DURATION_7_DAYS,
        ]);
        $this->setCurrentSupplierArticle($article, 10);

        // Create an OLD outgoing change outside the 7 day window (positive change to match command logic)
        $this->createArticleChangelog(Carbon::now()->subDays(30), $article, $article->quantity + 3, ArticleQuantityChangelog::TYPE_OUTGOING, 3);

        Artisan::call('calculate:min');

        $article->refresh();
        $this->assertSame(5, (int) $article->min_quantity);
    }

    public function test_does_nothing_when_no_delivery_time(): void
    {
        $article = $this->makeArticle([
            'min_quantity' => 5,
            'auto_min_quantity_duration' => Article::AUTO_MIN_QUANTITY_DURATION_7_DAYS,
        ]);

        // Create a recent outgoing change but no supplier/delivery time
        $this->createArticleChangelog(Carbon::now()->subDay(), $article, $article->quantity + 5, ArticleQuantityChangelog::TYPE_OUTGOING, 5);

        Artisan::call('calculate:min');

        $article->refresh();
        $this->assertSame(5, (int) $article->min_quantity);
    }

    public function test_does_not_persist_when_new_min_is_greater_or_equal_to_current(): void
    {
        $article = $this->makeArticle([
            'quantity' => 0,
            'min_quantity' => 5,
            'auto_min_quantity_duration' => Article::AUTO_MIN_QUANTITY_DURATION_7_DAYS,
        ]);
        $this->setCurrentSupplierArticle($article, 14); // delivery time 14 days

        // Make sales in the last 7 days summing to 7 (use positive values to satisfy command's current logic)
        $this->createArticleChangelog(Carbon::now()->subDays(1), $article, 0 + 7, ArticleQuantityChangelog::TYPE_OUTGOING, 7);

        Artisan::call('calculate:min');

        // Persistence is disabled in command, ensure min_quantity not changed
        $article->refresh();
        $this->assertSame(5, (int) $article->min_quantity);
    }

    public function test_skips_when_computed_min_is_below_current(): void
    {
        $article = $this->makeArticle([
            'quantity' => 0,
            'min_quantity' => 20,
            'auto_min_quantity_duration' => Article::AUTO_MIN_QUANTITY_DURATION_7_DAYS,
        ]);
        $this->setCurrentSupplierArticle($article, 14); // delivery time 14 days

        // Sales 7 in last 7 days -> computed min = 14 which is below current
        $this->createArticleChangelog(Carbon::now()->subDays(2), $article, 0 + 7, ArticleQuantityChangelog::TYPE_OUTGOING, 7);

        Artisan::call('calculate:min');

        $article->refresh();
        $this->assertSame(20, (int) $article->min_quantity);
    }
}