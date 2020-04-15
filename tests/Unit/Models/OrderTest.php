<?php

namespace Tests\Unit\Services;

use Carbon\Carbon;
use Mss\Models\Delivery;
use Mss\Models\OrderItem;
use Tests\TestCase;
use Mss\Models\Order;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class OrderTest extends TestCase
{
    public function test_new_order_number_is_correct_if_last_is_lower_ten() {
        Carbon::setTestNow('2018-06-01 12:00:00');
        Order::unguard();
        $order = Order::create([
            'internal_order_number' => '1806016'
        ]);

        $this->assertEquals('1806017', $order->getNextInternalOrderNumber());
    }

    public function test_new_order_number_is_correct_if_last_is_close_to_ten() {
        Carbon::setTestNow('2018-06-01 12:00:00');
        Order::unguard();
        $order = Order::create([
            'internal_order_number' => '1806019'
        ]);

        $this->assertEquals('18060110', $order->getNextInternalOrderNumber());
    }

    public function test_new_order_number_is_correct_if_last_is_ten() {
        Carbon::setTestNow('2018-06-01 12:00:00');
        Order::unguard();
        Order::create([
            'internal_order_number' => '1806018'
        ]);
        Order::create([
            'internal_order_number' => '1806019'
        ]);
        $order = Order::create([
            'internal_order_number' => '18060110'
        ]);

        $this->assertEquals('18060111', $order->getNextInternalOrderNumber());
    }

    public function test_new_order_number_is_correct_if_last_is_over_ten() {
        Carbon::setTestNow('2018-06-01 12:00:00');
        Order::unguard();
        Order::create([
            'internal_order_number' => '1806018'
        ]);
        Order::create([
            'internal_order_number' => '1806019'
        ]);
        Order::create([
            'internal_order_number' => '18060110'
        ]);
        Order::create([
            'internal_order_number' => '18060111'
        ]);
        $order = Order::create([
            'internal_order_number' => '18060112'
        ]);

        $this->assertEquals('18060113', $order->getNextInternalOrderNumber());
    }

    public function test_fully_delivered_order() {
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

        $this->assertTrue($order->isFullyDelivered());
        $this->assertFalse($order->isPartiallyDelivered());
    }

    public function test_partially_delivered_order() {
        /** @var Order $order */
        $order = factory(Order::class)->create();
        $order->items()->createMany(
            factory(OrderItem::class, 2)->make()->toArray()
        );

        /** @var Delivery $delivery */
        $delivery = factory(Delivery::class)->create([
            'order_id' => $order->id
        ]);

        $delivery->items()->create([
            'article_id' => $order->items->first()->article_id,
            'quantity' => $order->items->first()->quantity
        ]);

        $this->assertTrue($order->isPartiallyDelivered());
        $this->assertFalse($order->isFullyDelivered());
    }
}