<?php

namespace Tests\Feature\Api;

use Laravel\Sanctum\Sanctum;
use Mss\Models\Article;
use Mss\Models\ArticleGroup;
use Mss\Models\ArticleGroupItem;
use Mss\Models\ArticleQuantityChangelog;
use Mss\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleGroupTest extends TestCase
{
    use WithFaker;

    public function test_getting_article_group_by_id_as_non_authorized_user() {
        Sanctum::actingAs(
            User::first(),
            []
        );

        $response = $this->get('/api/v1/article-group/'.ArticleGroup::first()->id);
        $response->assertForbidden();
    }

    public function test_getting_article_group_by_id_as_authorized_user() {
        Sanctum::actingAs(
            User::first(),
            [User::API_ABILITY_ARTICLE_GROUP_GET]
        );

        $response = $this->get('/api/v1/article-group/'.ArticleGroup::first()->id);
        $response->assertOk();
    }

    public function test_getting_article_group_filtered_by_external_article_number() {
        Sanctum::actingAs(
            User::first(),
            [User::API_ABILITY_ARTICLE_GROUP_GET]
        );

        $articleGroup = ArticleGroup::inRandomOrder()->first();
        $articleGroup->external_article_number = $this->faker->ean13;
        $articleGroup->save();

        $response = $this->get('/api/v1/article-group?filter[external_article_number]='.$articleGroup->external_article_number);
        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.id', $articleGroup->id);
    }

    public function test_changing_quantity_fails_due_missing_ability() {
        Sanctum::actingAs(
            User::first(),
            []
        );

        $response = $this->post('/api/v1/article-group/'.ArticleGroup::first()->id.'/changeQuantity');
        $response->assertForbidden();
    }

    public function test_changing_quantity_fails_due_invalid_change() {
        Sanctum::actingAs(
            User::first(),
            [User::API_ABILITY_ARTICLE_GROUP_EDIT]
        );

        $response = $this->post('/api/v1/article-group/'.ArticleGroup::first()->id.'/changeQuantity', [
            'type' => 123456
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $response = $this->post('/api/v1/article-group/'.ArticleGroup::first()->id.'/changeQuantity', [
            'change' => -1,
            'type' => 123456
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $response = $this->post('/api/v1/article-group/'.ArticleGroup::first()->id.'/changeQuantity', [
            'change' => 'foobar',
            'type' => 123456
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_changing_quantity_fails_due_wrong_type() {
        Sanctum::actingAs(
            User::first(),
            [User::API_ABILITY_ARTICLE_GROUP_EDIT]
        );

        $response = $this->post('/api/v1/article-group/'.ArticleGroup::first()->id.'/changeQuantity', [
            'change' => 1,
            'type' => 123456
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }



    public function test_changing_quantity_was_successful() {
        Sanctum::actingAs(
            User::first(),
            [User::API_ABILITY_ARTICLE_GROUP_EDIT]
        );

        $oldQuantities = [];

        /** @var ArticleGroup $articleGroup */
        $articleGroup = ArticleGroup::with('items.article')->inRandomOrder()->first();
        $articleGroup->items->each(function ($item) use (&$oldQuantities) {
            /** @var ArticleGroupItem $item */
            $item->article->quantity += ($item->quantity * 3);
            $item->article->save();

            $oldQuantities[$item->article->id] = $item->article->quantity;
        });

        $note = implode(' ', $this->faker->words(5));
        $response = $this->post('/api/v1/article-group/'.$articleGroup->id.'/changeQuantity', [
            'change' => 2,
            'type' => ArticleQuantityChangelog::TYPE_OUTGOING,
            'note' => $note
        ]);
        $response->assertOk();

        $articleGroup->refresh();

        $articleGroup->items->each(function ($item) use ($oldQuantities, $note) {
            /** @var ArticleGroupItem $item */
            $item->article->refresh();

            $this->assertEquals(($oldQuantities[$item->article->id] - ($item->quantity * 2)), $item->article->quantity);

            /** @var ArticleQuantityChangelog $changelog */
            $changelog = $item->article->quantityChangelogs()->latest()->first();

            $this->assertEquals(($item->quantity * -2), $changelog->change);
            $this->assertEquals($note, $changelog->note);
            $this->assertEquals(ArticleQuantityChangelog::TYPE_OUTGOING, $changelog->type);
        });
    }
}
