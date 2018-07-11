<?php

namespace Tests\Unit\Models;

use Carbon\Carbon;
use Tests\TestCase;
use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ArticleTest extends TestCase {
    use DatabaseMigrations, HelperTrait;

    public function test_quantity_at_date_matches_article_quantity_as_it_doesnt_has_a_changelog_entry() {
        $date = Carbon::now()->subDay();
        /* @var $article Article */
        $article = factory(Article::class)->create([
            'created_at' => $date,
            'quantity' => 5
        ]);
        $article->load('audits');

        $this->assertEquals(5, $article->getAttributeAtDate('quantity', $date));
    }

    public function test_quantity_at_date_bus_article_has_been_created_after_requested_date() {
        $date = Carbon::now()->subDay();
        /* @var $article Article */
        $article = factory(Article::class)->create([
            'created_at' => Carbon::now()
        ]);
        $article->load('audits');

        $this->assertEquals(0, $article->getAttributeAtDate('quantity', $date));
    }

    public function test_quantity_at_date_last_changelog_entry() {
        $date = Carbon::now()->subDay(1);
        /* @var $article Article */
        $article = factory(Article::class)->create([
            'quantity' => 12,
            'created_at' => $date->copy()->subDay(3)
        ]);

        $this->createArticleChangelog($date->copy()->subDay(2), $article, 12, ArticleQuantityChangelog::TYPE_INCOMING, 7);

        $article->load('audits');

        $this->assertEquals(12, $article->getAttributeAtDate('quantity', $date));
    }

    public function test_quantity_at_date_requested_day_does_not_count() {
        /* @var $article Article */
        $article = factory(Article::class)->create([
            'quantity' => 12,
            'created_at' => Carbon::parse('2018-05-10 10:00:00')
        ]);

        $this->createArticleChangelog(Carbon::parse('2018-05-23 10:00:00'), $article, 10, ArticleQuantityChangelog::TYPE_OUTGOING, 1);
        $this->createArticleChangelog(Carbon::parse('2018-05-28 10:00:00'), $article, 9, ArticleQuantityChangelog::TYPE_OUTGOING, 1);
        $this->createArticleChangelog(Carbon::parse('2018-05-31 10:00:00'), $article, 8, ArticleQuantityChangelog::TYPE_OUTGOING, 1);
        $this->createArticleChangelog(Carbon::parse('2018-05-31 16:00:00'), $article, 7, ArticleQuantityChangelog::TYPE_OUTGOING, 1);
        $this->createArticleChangelog(Carbon::parse('2018-06-01 10:00:00'), $article, 6, ArticleQuantityChangelog::TYPE_OUTGOING, 1);

        $this->assertEquals(7, $article->getAttributeAtDate('quantity', Carbon::parse('2018-05-31')));
    }

    public function test_getAttributeAtDate_returns_correct_value_of_end_of_previous_day() {
        /* @var $article Article */
        $article = factory(Article::class)->create([
            'created_at' => Carbon::parse('2018-05-10 10:00:00'),
            'quantity' => 25,
            'status' => 0
        ]);

        $this->createArticleAudit($article, ['status' => 'aktiv'], ['status' => 'deaktiviert'], Carbon::parse('2018-05-23 10:00:00'));
        $this->createArticleAudit($article, ['status' => 'deaktiviert'], ['status' => 'aktiv'], Carbon::parse('2018-05-28 10:00:00'));
        $this->createArticleAudit($article, ['status' => 'aktiv'], ['status' => 'deaktiviert'], Carbon::parse('2018-05-31 10:00:00'));
        $this->createArticleAudit($article, ['status' => 'deaktiviert'], ['status' => 'aktiv'], Carbon::parse('2018-05-31 16:00:00'));
        $this->createArticleAudit($article, ['status' => 'aktiv'], ['status' => 'deaktiviert'], Carbon::parse('2018-06-01 10:00:00'));

        $this->assertEquals(1, $article->getAttributeAtDate('status', Carbon::parse('2018-05-31')));
    }

    public function test_getAttributeAtDate_returns_null_as_request_date_is_before_creation_date() {
        /* @var $article Article */
        $article = factory(Article::class)->create([
            'created_at' => Carbon::now()->subWeeks(2),
            'quantity' => 25,
            'status' => 0
        ]);

        $this->createArticleAudit($article, ['status' => 'aktiv'], ['status' => 'deaktiviert'], Carbon::now()->subWeeks(1));

        $this->assertEquals(null, $article->getAttributeAtDate('status', Carbon::now()->subWeeks(3)));
    }

    public function test_getAttributeAtDate_returns_old_status_as_request_date_is_before_first_change() {
        /* @var $article Article */
        $article = factory(Article::class)->create([
            'created_at' => Carbon::now()->subWeeks(3),
            'quantity' => 25,
            'status' => 0
        ]);

        $this->createArticleAudit($article, ['status' => 'aktiv'], ['status' => 'deaktiviert'], Carbon::now()->subWeeks(1));

        $this->assertEquals(1, $article->getAttributeAtDate('status', Carbon::now()->subWeeks(2)));
    }

    public function test_getAttributeAtDate_returns_correct_status_at_specific_date_between_changes() {
        /* @var $article Article */
        $article = factory(Article::class)->create([
            'created_at' => Carbon::now()->subWeeks(5),
            'quantity' => 25,
            'status' => 0
        ]);

        $this->createArticleAudit($article, ['status' => 'aktiv'], ['status' => 'deaktiviert'], Carbon::now()->subWeeks(4));
        $this->createArticleAudit($article, ['status' => 'deaktiviert'], ['status' => 'aktiv'], Carbon::now()->subWeeks(3));
        $this->createArticleAudit($article, ['status' => 'aktiv'], ['status' => 'deaktiviert'], Carbon::now()->subWeeks(1));

        $this->assertEquals(1, $article->getAttributeAtDate('status', Carbon::now()->subWeeks(2)));
    }

    public function test_getAttributeAtDate_returns_current_status_as_requested_date_is_after_last_change() {
        /* @var $article Article */
        $article = factory(Article::class)->create([
            'created_at' => Carbon::now()->subWeeks(3),
            'quantity' => 25,
            'status' => 0
        ]);

        $this->createArticleAudit($article, ['status' => 'aktiv'], ['status' => 'deaktiviert'], Carbon::now()->subWeeks(1));

        $this->assertEquals(0, $article->getAttributeAtDate('status', Carbon::now()));
    }
}