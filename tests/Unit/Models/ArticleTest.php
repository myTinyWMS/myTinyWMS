<?php

namespace Tests\Unit\Services;

use Carbon\Carbon;
use Tests\TestCase;
use Mss\Models\Article;
use Tests\Unit\Models\HelperTrait;
use Mss\Models\ArticleQuantityChangelog;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ArticleTest extends TestCase
{
    use DatabaseMigrations, HelperTrait;

    public function test_quantity_at_date_specific_changelog_entry() {
        /* @var $article Article */
        $article = factory(Article::class)->create([
            'quantity' => 20,
            'created_at' => Carbon::now()->subDays(6)
        ]);

        $this->createArticleChangelog(Carbon::now()->subDays(5), $article, 5, ArticleQuantityChangelog::TYPE_INCOMING, 5);
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
}