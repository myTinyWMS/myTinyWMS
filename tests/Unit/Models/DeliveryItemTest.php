<?php

namespace Tests\Unit\Models;

use Carbon\Carbon;
use Mss\Models\Delivery;
use Mss\Models\OrderItem;
use Tests\TestCase;
use Mss\Models\Order;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DeliveryItemTest extends TestCase
{
    public function test_delivery_has_been_deleted_if_last_delivery_item_has_been_deleted() {
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

        $deliveryItems = $delivery->items()->get();

        $deliveryItems->get(0)->delete();

        $this->assertInstanceOf(Delivery::class, Delivery::find($delivery->id));

        $deliveryItems->get(1)->delete();

        $this->assertNull(Delivery::find($delivery->id));
    }
}