<?php

namespace Tests\Unit\Services;

use Carbon\Carbon;
use Tests\TestCase;
use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ArticleTest extends TestCase
{
    use DatabaseMigrations;

    public function test_quantity_at_date_matches_article_quantity_as_it_doesnt_has_a_changelog_entry() {
        $date = Carbon::now()->subDay();
        /* @var $article Article */
        $article = factory(Article::class)->create();
        $article = Article::where('id', $article->id)->withQuantityAtDate($date, 'current_quantity')->first();

        $this->assertEquals($article->getQuantityAtDate($date, 'current_quantity'), $article->quantity);
    }

    public function test_quantity_at_date_last_changelog_entry() {
        $date = Carbon::now()->subDay();
        /* @var $article Article */
        $article = factory(Article::class)->create([
            'quantity' => 5
        ]);
        ArticleQuantityChangelog::create([
            'created_at' => $date->copy()->subDay(1),
            'updated_at' => $date->copy()->subDay(1),
            'article_id' => $article->id,
            'new_quantity' => 12,
            'user_id' => 1,
            'type' => ArticleQuantityChangelog::TYPE_INCOMING,
            'change' => 7
        ]);
        $article = Article::where('id', $article->id)->withQuantityAtDate($date, 'current_quantity')->first();

        $this->assertEquals($article->getQuantityAtDate($date, 'current_quantity'), 12);
    }

    public function test_quantity_at_date_specific_changelog_entry() {
        $date1 = Carbon::now()->subDay(1);
        $date2 = Carbon::now()->subDay(2);
        $date3 = Carbon::now()->subDay(3);
        $date4 = Carbon::now()->subDay(4);
        /* @var $article Article */
        $article = factory(Article::class)->create([
            'quantity' => 5
        ]);

        ArticleQuantityChangelog::create([
            'created_at' => $date1,
            'updated_at' => $date1,
            'article_id' => $article->id,
            'new_quantity' => 20,
            'user_id' => 1,
            'type' => ArticleQuantityChangelog::TYPE_INCOMING,
            'change' => 5
        ]);

        ArticleQuantityChangelog::create([
            'created_at' => $date2,
            'updated_at' => $date2,
            'article_id' => $article->id,
            'new_quantity' => 15,
            'user_id' => 1,
            'type' => ArticleQuantityChangelog::TYPE_INCOMING,
            'change' => 5
        ]);

        ArticleQuantityChangelog::create([
            'created_at' => $date3,
            'updated_at' => $date3,
            'article_id' => $article->id,
            'new_quantity' => 10,
            'user_id' => 1,
            'type' => ArticleQuantityChangelog::TYPE_INCOMING,
            'change' => 5
        ]);

        ArticleQuantityChangelog::create([
            'created_at' => $date4,
            'updated_at' => $date4,
            'article_id' => $article->id,
            'new_quantity' => 5,
            'user_id' => 1,
            'type' => ArticleQuantityChangelog::TYPE_INCOMING,
            'change' => 5
        ]);

        $article = Article::where('id', $article->id)->withQuantityAtDate($date3, 'current_quantity')->first();

        $this->assertEquals($article->getQuantityAtDate($date3, 'current_quantity'), 10);
    }


    public function test_changelog_sum_in_date_range() {
        /* @var $article Article */
        $article = factory(Article::class)->create([
            'quantity' => 25
        ]);

        ArticleQuantityChangelog::create([
            'created_at' => Carbon::parse('2018-04-30 12:10:00'),
            'updated_at' => Carbon::parse('2018-04-30 12:10:00'),
            'article_id' => $article->id,
            'new_quantity' => 5,
            'user_id' => 1,
            'type' => ArticleQuantityChangelog::TYPE_INCOMING,
            'change' => 5
        ]);

        ArticleQuantityChangelog::create([
            'created_at' => Carbon::parse('2018-05-01 10:00:00'),
            'updated_at' => Carbon::parse('2018-05-01 10:00:00'),
            'article_id' => $article->id,
            'new_quantity' => 10,
            'user_id' => 1,
            'type' => ArticleQuantityChangelog::TYPE_INCOMING,
            'change' => 5
        ]);

        ArticleQuantityChangelog::create([
            'created_at' => Carbon::parse('2018-05-10 10:00:00'),
            'updated_at' => Carbon::parse('2018-05-10 10:00:00'),
            'article_id' => $article->id,
            'new_quantity' => 15,
            'user_id' => 1,
            'type' => ArticleQuantityChangelog::TYPE_INCOMING,
            'change' => 5
        ]);

        ArticleQuantityChangelog::create([
            'created_at' => Carbon::parse('2018-05-31 10:00:00'),
            'updated_at' => Carbon::parse('2018-05-31 10:00:00'),
            'article_id' => $article->id,
            'new_quantity' => 20,
            'user_id' => 1,
            'type' => ArticleQuantityChangelog::TYPE_INCOMING,
            'change' => 5
        ]);

        ArticleQuantityChangelog::create([
            'created_at' => Carbon::parse('2018-06-01 10:00:00'),
            'updated_at' => Carbon::parse('2018-06-01 10:00:00'),
            'article_id' => $article->id,
            'new_quantity' => 25,
            'user_id' => 1,
            'type' => ArticleQuantityChangelog::TYPE_INCOMING,
            'change' => 5
        ]);

        $start = Carbon::parse('2018-05-01');
        $end = $start->copy()->lastOfMonth();

        $article = Article::where('id', $article->id)->withChangelogSumInDateRange($start, $end, ArticleQuantityChangelog::TYPE_INCOMING, 'total_incoming')->first();
        $this->assertEquals(15, $article->total_incoming);
    }

}