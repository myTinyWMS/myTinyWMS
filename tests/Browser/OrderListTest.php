<?php

namespace Tests\Browser;

use Mss\Models\Article;
use Mss\Models\Order;
use Mss\Models\OrderItem;
use Mss\Models\OrderMessage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class OrderListTest extends DuskTestCase
{
    /**
     * login before all other tests
     *
     * @throws \Throwable
     */
    public function test_login() {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 2000);
            $this->login($browser);
        });
    }

    public function test_unassigned_message_note_is_shown() {
        OrderMessage::where('read', 0)->update(['read' => 1]);
        $message = factory(OrderMessage::class)->create(['read' => 0, 'order_id' => null]);

        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/order')
                ->assertSee('Bestellungen')
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->assertSee('1 nicht zugeordnete neue Nachricht');
        });
    }

    public function test_assigned_message_note_is_shown() {
        OrderMessage::where('read', 0)->update(['read' => 1]);
        $message = factory(OrderMessage::class)->create(['read' => 0]);

        $this->browse(function (Browser $browser) use ($message) {
            $browser
                ->visit('/order')
                ->assertSee('Bestellungen')
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->assertSee('Neue Nachrichten zu folgenden Bestellungen')
                ->assertSee($message->internal_order_number.' bei '.$message->order->supplier->name);
        });
    }

    public function test_filter_by_supplier() {
        $order1 = Order::statusOpen()->whereNotNull('supplier_id')->inRandomOrder()->first();
        $order2 = Order::statusOpen()->whereNotNull('supplier_id')->where('supplier_id', '!=', $order1->supplier_id)->inRandomOrder()->first();

        $this->browse(function (Browser $browser) use ($order1, $order2) {
            $browser
                ->visit('/order')
                ->assertSee('Bestellungen')
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->assertSee($order1->internal_order_number)
                ->assertSee($order2->internal_order_number)
                ->click('#table-filter')
                ->select('#filterSupplier', $order1->supplier_id)
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->assertSeeIn('#dataTableBuilder tbody', $order1->internal_order_number)
                ->assertDontSeeIn('#dataTableBuilder tbody', $order2->internal_order_number);
        });
    }

    public function test_filter_by_status() {
        $order = Order::statusOpen()->whereNotNull('supplier_id')->inRandomOrder()->first();
        $order->status = Order::STATUS_DELIVERED;
        $order->save();

        $this->browse(function (Browser $browser) use ($order) {
            $browser
                ->visit('/order')
                ->assertSee('Bestellungen')
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->assertDontSeeIn('#dataTableBuilder tbody', $order->internal_order_number)
                ->click('#table-filter')
                ->select('#filterStatus', Order::STATUS_DELIVERED)
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->assertSeeIn('#dataTableBuilder tbody', $order->internal_order_number);
        });
    }

    public function test_filter_by_invoice_status() {
        $order = Order::statusOpen()->whereNotNull('supplier_id')->inRandomOrder()->first();
        $order->items->each(function ($item) {
            $item->invoice_received = OrderItem::INVOICE_STATUS_RECEIVED;
            $item->save();
        });

        $this->browse(function (Browser $browser) use ($order) {
            $browser
                ->visit('/order')
                ->assertSee('Bestellungen')
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->assertSeeIn('#dataTableBuilder tbody', $order->internal_order_number)
                ->click('#table-filter')
                ->select('#filterInvoiceStatus', 'all')
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->assertSeeIn('#dataTableBuilder tbody', $order->internal_order_number);
        });
    }

    public function test_filter_by_ab_status() {
        $order = Order::statusOpen()->whereNotNull('supplier_id')->inRandomOrder()->first();
        $order->items->each(function ($item) {
            $item->confirmation_received = true;
            $item->save();
        });

        $this->browse(function (Browser $browser) use ($order) {
            $browser
                ->visit('/order')
                ->assertSee('Bestellungen')
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->assertSeeIn('#dataTableBuilder tbody', $order->internal_order_number)
                ->click('#table-filter')
                ->select('#filterABStatus', 'all')
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->assertSeeIn('#dataTableBuilder tbody', $order->internal_order_number);
        });
    }

    public function test_search_by_order_number() {
        $this->browse(function (Browser $browser) {
            $order1 = Order::statusOpen()->doesntHave('messages')->whereNotNull('supplier_id')->inRandomOrder()->first();
            $order2 = Order::statusOpen()->doesntHave('messages')->where('id', '!=', $order1->id)->whereNotNull('supplier_id')->inRandomOrder()->first();

            $browser
                ->visit('/order')
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->assertSee($order1->internal_order_number)
                ->assertSee($order2->internal_order_number)
                ->type('#dataTableBuilder_filter input', $order1->internal_order_number)
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->waitUntilMissingText('Bitte warten')
                ->assertSee($order1->internal_order_number)
                ->assertDontSee($order2->internal_order_number)
            ;
        });
    }
}