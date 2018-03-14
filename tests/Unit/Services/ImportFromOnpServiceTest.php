<?php

namespace Tests\Unit\Services;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Mss\Console\Commands\ImportCommand;
use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;
use Mss\Models\Category;
use Mss\Models\Legacy\MaterialLog;
use Mss\Models\Supplier;
use Mss\Services\ImportFromOnpService;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;
use Tests\TestCase;
use Mss\Models\Legacy\Category as LegacyCategory;
use Mss\Models\Legacy\Supplier as LegacySupplier;
use Mss\Models\Legacy\Material as LegacyArticle;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mss\Models\Legacy\MaterialLog as LegacyArticleLog;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Tester\CommandTester;

class ImportFromOnpServiceTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

    protected $connectionsToTransact = ['onp','primary'];

    /**
     * @test
     */
    public function it_creates_categories_from_legacy_categories() {
        LegacyCategory::truncate();
        $legacyCategories = factory(LegacyCategory::class, 5)->create();

        $mock = $this->createPartialMock(LegacyCategory::class, ['all']);
        $mock->method('all')->willReturn($legacyCategories);

        $bar = new ProgressBar(new NullOutput());
        $service = new ImportFromOnpService(new Command());
        $service->importCategories($bar);

        $legacyCategories->each(function ($legacyCategory) {
            $this->assertEquals(1, Category::where('name', $legacyCategory->name)->count());
        });
    }

    /**
     * @test
     */
    public function it_creates_suppliers_from_legacy_suppliers() {
        LegacySupplier::truncate();
        $legacySupplier = factory(LegacySupplier::class, 5)->create();

        $bar = new ProgressBar(new NullOutput());
        $service = new ImportFromOnpService(new Command());
        $service->importSuppliers($bar);

        $legacySupplier->each(function ($legacySupplier) {
            $this->assertEquals(1, Supplier::where('name', $legacySupplier->company_name)->count());
        });
    }

    /**
     * @test
     */
    public function it_creates_article_log_from_legacy_log() {
        LegacyArticleLog::truncate();
        $legacyArticleLog = factory(LegacyArticleLog::class, 5)->create();

        $bar = new ProgressBar(new NullOutput());
        $service = new ImportFromOnpService(new Command());
        $service->importArticles($bar);
        $service->importLog($bar);

        $legacyArticleLog->each(function ($legacyArticleLog) {
            $this->assertEquals(1, ArticleQuantityChangelog::where('article_id', $legacyArticleLog->material_id)->count());
        });
    }

    /**
     * @test
     */
    public function it_creates_articles_from_legacy_material() {
        LegacyArticle::truncate();
        $legacySupplier = factory(LegacySupplier::class)->create();
        $legacyArticle = factory(LegacyArticle::class, 5)->create([
            'maschinenzugehoerigkeit' => 'test',
            'hersteller' => $legacySupplier->id
        ]);

        $bar = new ProgressBar(new NullOutput());
        $service = new ImportFromOnpService(new Command());
        $service->importSuppliers($bar);
        $service->importArticles($bar);

        $legacyArticle->each(function ($legacyArticle) use ($legacySupplier) {
            $this->assertEquals(1, Article::where('name', $legacyArticle->artikelbezeichnung)->count());
            $article = Article::where('name', $legacyArticle->artikelbezeichnung)->withCurrentSupplier()->first();
            $this->assertEquals(1, $article->tags->where('name', 'test')->count());
            $this->assertEquals($article->currentSupplier->name, $legacySupplier->company_name);
        });
    }
}
