<?php

namespace Tests\Unit\Services;

use Carbon\Carbon;
use Mss\Models\Delivery;
use Mss\Models\OrderItem;
use Tests\TestCase;
use Mss\Models\Order;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DeliveryTest extends TestCase
{
    public function test_order_is_still_delivered_if_duplicate_delivery_has_been_deleted() {
        /** @var Order $order */
        $order = factory(Order::class)->create();
        $order->items()->createMany(
            factory(OrderItem::class, 2)->make()->toArray()
        );

        /** @var Delivery $delivery1 */
        $delivery1 = factory(Delivery::class)->create([
            'order_id' => $order->id
        ]);

        $order->items->each(function ($orderItem) use ($delivery1) {
            $delivery1->items()->create([
                'article_id' => $orderItem->article_id,
                'quantity' => $orderItem->quantity
            ]);
        });

        /** @var Delivery $delivery2 */
        $delivery2 = Delivery::create([
            'order_id' => $order->id,
            'delivery_date' => now(),
        ]);

        $order->items->each(function ($orderItem) use ($delivery2) {
            $delivery2->items()->create([
                'article_id' => $orderItem->article_id,
                'quantity' => $orderItem->quantity
            ]);
        });

        $delivery2->delete();

        $order = Order::find($order->id);

        $this->assertEquals(Order::STATUS_DELIVERED, $order->status);
    }

    public function test_order_is_reset_to_partially_delivered_if_second_delivery_has_been_deleted() {
        /** @var Order $order */
        $order = factory(Order::class)->create([
            'status' => Order::STATUS_DELIVERED
        ]);
        $order->items()->createMany(
            factory(OrderItem::class, 2)->make()->toArray()
        );

        /** @var Delivery $delivery1 */
        $delivery1 = factory(Delivery::class)->create([
            'order_id' => $order->id
        ]);

        $delivery1->items()->create([
            'article_id' => $order->items->get(0)->article_id,
            'quantity' => $order->items->get(0)->quantity
        ]);

        /** @var Delivery $delivery2 */
        $delivery2 = Delivery::create([
            'order_id' => $order->id,
            'delivery_date' => now(),
        ]);

        $delivery2->items()->create([
            'article_id' => $order->items->get(1)->article_id,
            'quantity' => $order->items->get(1)->quantity
        ]);

        $delivery2->delete();

        $order = Order::find($order->id);

        $this->assertEquals(Order::STATUS_PARTIALLY_DELIVERED, $order->status);
    }

    public function test_order_is_reset_to_order_if_last_delivery_has_been_deleted() {
        /** @var Order $order */
        $order = factory(Order::class)->create([
            'status' => Order::STATUS_DELIVERED
        ]);
        $order->items()->createMany(
            factory(OrderItem::class, 2)->make()->toArray()
        );

        /** @var Delivery $delivery1 */
        $delivery1 = factory(Delivery::class)->create([
            'order_id' => $order->id
        ]);

        $order->items->each(function ($orderItem) use ($delivery1) {
            $delivery1->items()->create([
                'article_id' => $orderItem->article_id,
                'quantity' => $orderItem->quantity
            ]);
        });

        $delivery1->delete();

        $order = Order::find($order->id);

        $this->assertEquals(Order::STATUS_ORDERED, $order->status);
    }
}