<?php

namespace Tests\Unit\Services;

use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;
use Mss\Models\Delivery;
use Mss\Models\OrderItem;
use Mss\Models\User;
use Tests\TestCase;
use Mss\Models\Order;

class ArticleQuantityChangelogTest extends TestCase
{
    public function test_delivery_item_are_being_deleted_on_deleting_changelogs() {
        $delivery = $this->createTestDelivery();

        $this->be(User::first());

        // change quantity
        $delivery->items->get(0)->article->changeQuantity(1, ArticleQuantityChangelog::TYPE_INCOMING, '', $delivery->items->get(0));
        /** @var ArticleQuantityChangelog $changelog1 */
        $changelog1 = ArticleQuantityChangelog::where('delivery_item_id', $delivery->items->get(0)->id)->first();

        $delivery->items->get(1)->article->changeQuantity(1, ArticleQuantityChangelog::TYPE_INCOMING, '', $delivery->items->get(1));
        /** @var ArticleQuantityChangelog $changelog2 */
        $changelog2 = ArticleQuantityChangelog::where('delivery_item_id', $delivery->items->get(1)->id)->first();

        $changelog1->delete();

        $this->assertEquals(1, $delivery->items()->count());

        $changelog2->delete();

        $this->assertEquals(0, $delivery->items()->count());
    }

    public function test_quantity_is_being_reset_on_deleting_changelogs() {
        $delivery = $this->createTestDelivery();

        $this->be(User::first());

        $oldQuantity1 = $delivery->items->get(0)->article->quantity;
        $delivery->items->get(0)->article->changeQuantity(3, ArticleQuantityChangelog::TYPE_INCOMING, '', $delivery->items->get(0));
        /** @var ArticleQuantityChangelog $changelog1 */
        $changelog1 = ArticleQuantityChangelog::where('delivery_item_id', $delivery->items->get(0)->id)->first();

        $oldQuantity2 = $delivery->items->get(1)->article->quantity;
        $delivery->items->get(1)->article->changeQuantity(5, ArticleQuantityChangelog::TYPE_INCOMING, '', $delivery->items->get(1));
        /** @var ArticleQuantityChangelog $changelog2 */
        $changelog2 = ArticleQuantityChangelog::where('delivery_item_id', $delivery->items->get(1)->id)->first();

        // check quantity article 1
        $delivery->items->get(0)->article->refresh();
        $this->assertEquals(($oldQuantity1 + 3), $delivery->items->get(0)->article->quantity);

        // check quantity article 2
        $delivery->items->get(1)->article->refresh();
        $this->assertEquals(($oldQuantity2 + 5), $delivery->items->get(1)->article->quantity);

        // reset and re-check article 1
        $changelog1->delete();
        $delivery->items->get(0)->article->refresh();
        $this->assertEquals($oldQuantity1, $delivery->items->get(0)->article->quantity);

        // reset and re-check article 2
        $changelog2->delete();
        $delivery->items->get(1)->article->refresh();
        $this->assertEquals($oldQuantity2, $delivery->items->get(1)->article->quantity);
    }

    /**
     * @return Delivery
     */
    protected function createTestDelivery() {
        /** @var Order $order */
        $order = factory(Order::class)->create();
        $order->items()->createMany(
            factory(OrderItem::class, 2)->make()->toArray()
        );

        /** @var Delivery $delivery */
        $delivery = factory(Delivery::class)->create([
            'order_id' => $order->id
        ]);

        $order->items->each(function ($orderItem) use ($delivery) {
            $delivery->items()->create([
                'article_id' => $orderItem->article_id,
                'quantity' => $orderItem->quantity
            ]);
        });

        return Delivery::find($delivery->id);
    }
}