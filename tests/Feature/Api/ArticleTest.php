<?php

namespace Tests\Feature\Api;

use Laravel\Sanctum\Sanctum;
use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;
use Mss\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleTest extends TestCase
{
    use WithFaker;

    public function test_getting_article_by_id_as_non_authorized_user() {
        Sanctum::actingAs(
            User::first(),
            []
        );

        $response = $this->get('/api/v1/article/'.Article::first()->id);
        $response->assertForbidden();
    }

    public function test_getting_article_by_id_as_authorized_user() {
        Sanctum::actingAs(
            User::first(),
            [User::API_ABILITY_ARTICLE_GET]
        );

        $response = $this->get('/api/v1/article/'.Article::first()->id);
        $response->assertOk();
    }

    public function test_getting_articles_filtered_by_internal_article_number() {
        Sanctum::actingAs(
            User::first(),
            [User::API_ABILITY_ARTICLE_GET]
        );

        $article = Article::whereNotNull('internal_article_number')->first();

        $response = $this->get('/api/v1/article?filter[internal_article_number]='.$article->internal_article_number);
        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.id', $article->id);
    }

    public function test_getting_articles_filtered_by_external_article_number() {
        Sanctum::actingAs(
            User::first(),
            [User::API_ABILITY_ARTICLE_GET]
        );

        $article = Article::inRandomOrder()->first();
        $article->external_article_number = $this->faker->ean13;
        $article->save();

        $response = $this->get('/api/v1/article?filter[external_article_number]='.$article->external_article_number);
        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.id', $article->id);
    }

    public function test_getting_articles_filtered_by_category_id() {
        Sanctum::actingAs(
            User::first(),
            [User::API_ABILITY_ARTICLE_GET]
        );

        $article = Article::inRandomOrder()->first();

        $response = $this->get('/api/v1/article?filter[category_id]='.$article->category_id);
        $response->assertOk();
        $response->assertJsonCount(Article::active()->where('category_id', $article->category_id)->count());
    }

    public function test_getting_articles_filtered_by_inventory() {
        Sanctum::actingAs(
            User::first(),
            [User::API_ABILITY_ARTICLE_GET]
        );

        $article = Article::inRandomOrder()->first();

        $response = $this->get('/api/v1/article?filter[inventory]='.$article->inventory);
        $response->assertOk();
        $response->assertJsonCount(Article::active()->where('inventory', $article->inventory)->count());
    }

    public function test_getting_articles_filtered_by_packaging_category() {
        Sanctum::actingAs(
            User::first(),
            [User::API_ABILITY_ARTICLE_GET]
        );

        $article = Article::inRandomOrder()->first();

        $response = $this->get('/api/v1/article?filter[packaging_category]='.$article->packaging_category);
        $response->assertOk();
        $response->assertJsonCount(Article::active()->where('packaging_category', $article->packaging_category)->count());
    }

    public function test_getting_articles_filtered_by_packaging_cost_center() {
        Sanctum::actingAs(
            User::first(),
            [User::API_ABILITY_ARTICLE_GET]
        );

        $articles = Article::inRandomOrder()->take(3)->get();

        $article1 = $articles[0];
        $article1->cost_center = $this->faker->numberBetween(1000, 2000);
        $article1->save();

        $article2 = $articles[1];
        $article2->cost_center = $article1->cost_center;
        $article2->save();

        $article3 = $articles[2];
        $article3->cost_center = $article1->cost_center;
        $article3->save();

        $response = $this->get('/api/v1/article?filter[cost_center]='.$article1->cost_center);
        $response->assertOk();
        $response->assertJsonCount(3);
    }

    public function test_getting_article_by_id_with_only_some_fields() {
        Sanctum::actingAs(
            User::first(),
            [User::API_ABILITY_ARTICLE_GET]
        );

        $article = Article::first();

        $response = $this->get('/api/v1/article?filter[id]='.$article->id.'&fields[articles]=id,quantity');
        $response->assertOk();
        $response->assertExactJson([
            ['id' => $article->id, 'quantity' => $article->quantity]
        ]);
    }
    
    public function test_changing_quantity_fails_due_missing_ability() {
        Sanctum::actingAs(
            User::first(),
            []
        );

        $response = $this->post('/api/v1/article/'.Article::first()->id.'/changeQuantity');
        $response->assertForbidden();
    }

    public function test_changing_quantity_fails_due_invalid_change() {
        Sanctum::actingAs(
            User::first(),
            [User::API_ABILITY_ARTICLE_EDIT]
        );

        $response = $this->post('/api/v1/article/'.Article::first()->id.'/changeQuantity', [
            'type' => 123456
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $response = $this->post('/api/v1/article/'.Article::first()->id.'/changeQuantity', [
            'change' => -1,
            'type' => 123456
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $response = $this->post('/api/v1/article/'.Article::first()->id.'/changeQuantity', [
            'change' => 'foobar',
            'type' => 123456
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_changing_quantity_fails_due_wrong_type() {
        Sanctum::actingAs(
            User::first(),
            [User::API_ABILITY_ARTICLE_EDIT]
        );

        $response = $this->post('/api/v1/article/'.Article::first()->id.'/changeQuantity', [
            'change' => 1,
            'type' => 123456
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_changing_quantity_was_successful() {
        Sanctum::actingAs(
            User::first(),
            [User::API_ABILITY_ARTICLE_EDIT]
        );

        /** @var Article $article */
        $article = Article::where('quantity', '>', 2)->inRandomOrder()->first();
        $oldQuantity = $article->quantity;

        $response = $this->post('/api/v1/article/'.$article->id.'/changeQuantity', [
            'change' => 2,
            'type' => ArticleQuantityChangelog::TYPE_OUTGOING
        ]);
        $response->assertOk();

        $article->refresh();
        $this->assertEquals(($oldQuantity - 2), $article->quantity);

        /** @var ArticleQuantityChangelog $changelog */
        $changelog = $article->quantityChangelogs()->latest()->first();

        $this->assertEquals(-2, $changelog->change);
        $this->assertEquals('Api Update', $changelog->note);
        $this->assertEquals(ArticleQuantityChangelog::TYPE_OUTGOING, $changelog->type);
    }
}
