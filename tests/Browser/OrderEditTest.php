<?php


use Carbon\Carbon;
use Mss\Models\Order;
use Mss\Models\OrderItem;
use Mss\Models\User;
use Mss\Models\UserSettings;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class OrderEditTest extends DuskTestCase
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

    public function test_order_shows_needed_informations() {
        $this->browse(function (Browser $browser) {
            /** @var Order $order */
            $order = Order::has('items', '<=', 3)->inRandomOrder()->with(['items.article', 'supplier'])->first();

            $browser
                ->visit('/order/'.$order->id)
                ->assertSee($order->internal_order_number)
                ->assertSee($order->supplier->name);

            foreach($order->items as $item) {
                $browser
                    ->assertSeeIn('#order-article-'.$item->id, $item->article->name)
                    ->assertSeeIn('#order-article-'.$item->id, $item->quantity)
                    ->assertSeeIn('#order-article-'.$item->id, number_format($item->price, 2, ',', '.'))
                    ->assertSeeIn('#order-article-'.$item->id, number_format($item->price * $item->quantity, 2, ',', '.'))
                    ->assertSeeIn('#order-article-'.$item->id, Carbon::parse($item->expected_delivery)->format('d.m.Y'))
                ;
            }
        });
    }

    public function test_changing_order_status() {
        $this->browse(function (Browser $browser) {
            /** @var Order $order */
            $order = Order::inRandomOrder()->first();
            $order->status = Order::STATUS_NEW;
            $order->save();

            $browser
                ->visit('/order/'.$order->id)
                ->click('.order-change-status')
                ->clickLink(Order::getStatusTexts()[Order::STATUS_ORDERED])
                ->waitForText('Status geändert.')
                ->assertSeeIn('.order-status', Order::getStatusTexts()[Order::STATUS_ORDERED])
            ;

            $order->refresh();
            $this->assertEquals(Order::STATUS_ORDERED, $order->status);
        });
    }

    public function test_changing_payment_method() {
        $this->browse(function (Browser $browser) {
            /** @var Order $order */
            $order = Order::inRandomOrder()->first();

            $browser
                ->visit('/order/'.$order->id)
                ->click('.order-change-payment-method')
                ->clickLink(Order::getPaymentStatusText()[Order::PAYMENT_STATUS_PAID_WITH_CREDIT_CARD])
                ->waitForText('Bezahlstatus geändert.')
                ->assertSeeIn('.payment-method', Order::getPaymentStatusText()[Order::PAYMENT_STATUS_PAID_WITH_CREDIT_CARD])
            ;

            $order->refresh();
            $this->assertEquals(Order::PAYMENT_STATUS_PAID_WITH_CREDIT_CARD, $order->payment_status);
        });
    }

    public function test_changing_confirmation_status() {
        $this->browse(function (Browser $browser) {
            /** @var Order $order */
            $order = Order::has('items', '>', 1)->inRandomOrder()->first();
            $item = $order->items->first();
            $item->confirmation_received = false;
            $item->save();

            $browser
                ->visit('/order/'.$order->id)
                ->assertSeeIn('#order-article-'.$item->id.' .confirmation-status', 'nicht erhalten')
                ->click('#order-article-'.$item->id.' .confirmation-status .dropdown-button')
                ->clickLink('erhalten')
                ->waitForText('Status der Auftragsbestätigung aktualisiert.')
                ->assertDontSeeIn('#order-article-'.$item->id.' .confirmation-status', 'nicht erhalten')
            ;

            $item->refresh();
            $this->assertEquals(true, $item->confirmation_received);
        });
    }

    public function test_changing_invoice_status_received_with_same_prices() {
        $this->browse(function (Browser $browser) {
            /** @var Order $order */
            $order = Order::has('items', '>', 1)->inRandomOrder()->first();
            $item = $order->items->first();
            $item->price = $item->article->getCurrentSupplierArticle()->price;
            $item->invoice_received = OrderItem::INVOICE_STATUS_OPEN;
            $item->save();

            $browser
                ->visit('/order/'.$order->id)
                ->assertSeeIn('#order-article-'.$item->id.' .invoice-status', 'nicht erhalten')
                ->click('#order-article-'.$item->id.' .invoice-status .dropdown-button')
                ->clickLink('erhalten')
                ->waitForText('Status der Rechnung aktualisiert.')
                ->assertDontSeeIn('#order-article-'.$item->id.' .invoice-status', 'nicht erhalten')
            ;

            $item->refresh();
            $this->assertEquals(OrderItem::INVOICE_STATUS_RECEIVED, $item->invoice_received);
        });
    }

    public function test_changing_invoice_status_to_check() {
        $this->browse(function (Browser $browser) {
            /** @var Order $order */
            $order = Order::has('items', '>', 1)->inRandomOrder()->first();
            $item = $order->items->first();
            $item->price = $item->article->getCurrentSupplierArticle()->price;
            $item->invoice_received = OrderItem::INVOICE_STATUS_OPEN;
            $item->save();

            User::first()->settings()->set(UserSettings::SETTING_NOTIFY_ON_INVOICE_CHECKS, true, true);

            $this->startMailTest();

            $browser
                ->visit('/order/'.$order->id)
                ->assertSeeIn('#order-article-'.$item->id.' .invoice-status', 'nicht erhalten')
                ->click('#order-article-'.$item->id.' .invoice-status .dropdown-button')
                ->clickLink('in Prüfung')
                ->waitForText('Rechnungsprüfung - Mail an Einkaufsteam')
                ->type('#invoice_check_note', 'lorem ipsum')
                ->click('#send-invoice-check-mail')
                ->waitForText('Status der Rechnung aktualisiert.')
                ->assertDontSeeIn('#order-article-'.$item->id.' .invoice-status', 'nicht erhalten')
            ;

            $this->assertMailsSent(1);

            $item->refresh();
            $this->assertEquals(OrderItem::INVOICE_STATUS_CHECK, $item->invoice_received);
        });
    }

    public function test_creating_delivery() {
        $this->browse(function (Browser $browser) {
            /** @var Order $order */
            $order = Order::has('items', '=', 1)->inRandomOrder()->first();
            $order->status = Order::STATUS_ORDERED;
            $order->save();

            $browser
                ->visit('/order/'.$order->id.'/create-delivery')
                ->waitForText('Neuer Wareneingang')
                ->type('#delivery_note_number', 'foo123')
                ->type('#notes', 'lorem ipsum')
                ->click('@set-full-quantity-'.$order->items->first()->id)
                ->assertValue('input[name="quantities['.$order->items->first()->article->id.']"]', $order->items->first()->quantity)
                ->click('#save-delivery')
                ->waitForText('Lieferung gespeichert.');

            $order->refresh();
            $this->assertEquals(1, $order->deliveries()->count());
            $this->assertEquals($order->items->first()->quantity, $order->deliveries->first()->items->first()->quantity);
            $this->assertEquals(Order::STATUS_DELIVERED, $order->status);
        });
    }

    public function test_creating_delivery_for_some_items_sets_correct_order_status() {
        $this->browse(function (Browser $browser) {
            /** @var Order $order */
            $order = Order::has('items', '>', 1)->inRandomOrder()->first();
            $order->status = Order::STATUS_ORDERED;
            $order->save();

            $browser
                ->visit('/order/'.$order->id.'/create-delivery')
                ->waitForText('Neuer Wareneingang')
                ->type('#delivery_note_number', 'foo123')
                ->type('#notes', 'lorem ipsum')
                ->click('@set-full-quantity-'.$order->items->first()->id)
                ->assertValue('input[name="quantities['.$order->items->first()->article->id.']"]', $order->items->first()->quantity)
                ->click('#save-delivery')
                ->waitForText('Lieferung gespeichert.');

            $order->refresh();
            $this->assertEquals(1, $order->deliveries()->count());
            $this->assertEquals($order->items->first()->quantity, $order->deliveries->first()->items->first()->quantity);
            $this->assertEquals(Order::STATUS_PARTIALLY_DELIVERED, $order->status);
        });
    }

    public function test_creating_delivery_for_all_items_sets_correct_order_status() {
        $this->browse(function (Browser $browser) {
            /** @var Order $order */
            $order = Order::has('items', '>', 1)->doesntHave('deliveries')->inRandomOrder()->first();
            $order->status = Order::STATUS_ORDERED;
            $order->save();

            $browser
                ->visit('/order/'.$order->id.'/create-delivery')
                ->waitForText('Neuer Wareneingang')
                ->type('#delivery_note_number', 'foo123')
                ->type('#notes', 'lorem ipsum');

            $order->items->each(function ($item) use ($browser) {
                $browser
                    ->click('@set-full-quantity-'.$item->id)
                    ->assertValue('input[name="quantities['.$item->article->id.']"]', $item->quantity);
            });

            $browser
                ->click('#save-delivery')
                ->waitForText('Lieferung gespeichert.');

            $order->refresh();
            $this->assertEquals(Order::STATUS_DELIVERED, $order->status);

            $browser
                ->visit('/order/'.$order->id);
        });
    }
}
