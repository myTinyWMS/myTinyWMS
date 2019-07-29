<?php


use Mss\Models\Order;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Mss\Models\OrderMessage;

class OrderMessagesTest extends DuskTestCase
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

    public function test_button_for_initial_email_is_shown_on_new_order() {
        $this->browse(function (Browser $browser) {
            /** @var Order $order */
            $order = Order::inRandomOrder()->first();
            $order->status = Order::STATUS_NEW;
            $order->save();

            $browser
                ->visit('/order/'.$order->id)
                ->assertSee('Bestellung per E-Mail an Lieferant schicken')
            ;

            $order->status = Order::STATUS_ORDERED;
            $order->save();

            $browser
                ->visit('/order/'.$order->id)
                ->assertDontSee('Bestellung per E-Mail an Lieferant schicken')
            ;
        });
    }

    public function test_creating_initial_email_for_order() {
        $this->browse(function (Browser $browser) {
            /** @var Order $order */
            $order = Order::inRandomOrder()->first();
            $order->status = Order::STATUS_NEW;
            $order->save();

            $this->startMailTest();

            $browser
                ->visit('/order/' . $order->id.'/message/new?sendorder=1')
                ->assertValue('#receiver', $order->supplier->email)
                ->assertValue('#subject', '['.$order->internal_order_number.'] Neue Bestellung')
                ->assertInputValueIsNot('input[name="body"]', '')
                ->click('#send-message')
                ->waitForText('Nachricht verschickt')
                ->assertSeeIn('.order-messages', '['.$order->internal_order_number.'] Neue Bestellung')
                ->assertSeeIn('.order-messages', $order->supplier->email)
            ;

            $order->refresh();
            $this->assertMailsSent(1);
            $this->assertEquals(Order::STATUS_ORDERED, $order->status);
        });
    }

    public function test_creating_email_for_order() {
        $this->browse(function (Browser $browser) {
            /** @var Order $order */
            $order = Order::inRandomOrder()->first();
            $order->status = Order::STATUS_PARTIALLY_DELIVERED;
            $order->save();

            $this->startMailTest();

            $browser
                ->visit('/order/' . $order->id.'/message/new')
                ->assertValue('#receiver', $order->supplier->email)
                ->assertValue('#subject', '')
                ->assertInputValue('input[name="body"]', '')
                ->type('#subject', 'foo bar')
                ->type('.note-editable', 'lorem ipsum')
                ->click('#send-message')
                ->waitForText('Nachricht verschickt')
                ->assertSeeIn('.order-messages', 'foo bar')
                ->assertSeeIn('.order-messages', $order->supplier->email)
            ;

            $order->refresh();
            $this->assertMailsSent(1);
            $this->assertEquals(Order::STATUS_PARTIALLY_DELIVERED, $order->status);
        });
    }

    public function test_forwarding_email() {
        $this->browse(function (Browser $browser) {
            /** @var Order $order */
            $order = Order::inRandomOrder()->doesntHave('messages')->first();
            $message = $order->messages()->save(factory(OrderMessage::class)->make());

            $this->startMailTest();

            $browser
                ->visit('/order/' . $order->id)
                ->assertSee($message->subject)
                ->click('.order-message-menu')
                ->clickLink('Weiterleiten')
                ->waitForText('Nachricht weiterleiten')
                ->type('#receiver', 'foo@example.com')
                ->click('#send-message')
                ->waitForText('Nachricht weitergeleitet')
            ;

            $this->assertMailsSent(1);
        });
    }

    public function test_answer_email() {
        $this->browse(function (Browser $browser) {
            /** @var Order $order */
            $order = Order::inRandomOrder()->doesntHave('messages')->first();
            $message = $order->messages()->save(factory(OrderMessage::class)->make());

            $this->startMailTest();

            $browser
                ->visit('/order/' . $order->id)
                ->assertSee($message->subject)
                ->click('.order-message-menu')
                ->clickLink('Antworten')
                ->waitForText('Neue Nachricht')
                ->click('#send-message')
                ->waitForText('Nachricht verschickt')
            ;

            $this->assertMailsSent(1);
        });
    }

    public function test_mark_email_read() {
        $this->browse(function (Browser $browser) {
            /** @var Order $order */
            $order = Order::inRandomOrder()->doesntHave('messages')->first();
            $message = $order->messages()->save(factory(OrderMessage::class)->make(['read' => 0]));

            $browser
                ->visit('/order/' . $order->id)
                ->assertSee($message->subject)
                ->click('.order-message-menu')
                ->clickLink('Gelesen')
                ->waitForText('Nachricht als gelesen markiert')
            ;

            $message->refresh();
            $this->assertEquals(true, $message->read);
        });
    }

    public function test_mark_email_unread() {
        $this->browse(function (Browser $browser) {
            /** @var Order $order */
            $order = Order::inRandomOrder()->doesntHave('messages')->first();
            $message = $order->messages()->save(factory(OrderMessage::class)->make(['read' => 1]));

            $browser
                ->visit('/order/' . $order->id)
                ->assertSee($message->subject)
                ->click('.order-message-menu')
                ->clickLink('Ungelesen')
                ->waitForText('Nachricht als ungelesen markiert')
            ;

            $message->refresh();
            $this->assertEquals(false, $message->read);
        });
    }

    public function test_delete_email() {
        $this->browse(function (Browser $browser) {
            /** @var Order $order */
            $order = Order::inRandomOrder()->doesntHave('messages')->first();
            $message = $order->messages()->save(factory(OrderMessage::class)->make());

            $this->assertEquals(1, $order->messages()->count());

            $browser
                ->visit('/order/' . $order->id)
                ->assertSee($message->subject)
                ->click('.order-message-menu')
                ->clickLink('Löschen')
            ;
            $browser->driver->switchTo()->alert()->accept();
            $browser->waitForText('Nachricht gelöscht');

            $this->assertEquals(0, $order->messages()->count());
        });
    }

    public function test_move_email_to_other_order() {
        $this->browse(function (Browser $browser) {
            /** @var Order $order */
            $order1 = Order::inRandomOrder()->doesntHave('messages')->first();
            $order2 = Order::inRandomOrder()->doesntHave('messages')->where('id', '!=', $order1->id)->first();
            $message = $order1->messages()->save(factory(OrderMessage::class)->make(['read' => 1]));

            $browser
                ->visit('/order/' . $order1->id)
                ->click('.order-message-menu')
                ->clickLink('Verschieben')
                ->waitForText('Nachricht zuordnen')
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->radio('orderid', $order2->id)
                ->click('#save-assign-order-message')
                ->waitForText('Nachricht verschoben')
            ;

            $message->refresh();
            $this->assertEquals($order2->id, $message->order_id);

            $this->assertEquals(0, $order1->messages()->count());
            $this->assertEquals(1, $order2->messages()->count());
        });
    }

    public function test_assign_unassigned_email() {
        $this->browse(function (Browser $browser) {
            /** @var Order $order */
            $order = Order::inRandomOrder()->doesntHave('messages')->first();
            $message = factory(OrderMessage::class)->make(['read' => 0]);
            $message->order_id = null;
            $message->save();

            $browser
                ->visit('/order')
                ->assertSee('1 nicht zugeordnete neue Nachricht')
                ->clickLink('mehr »')
                ->waitForText('Neue Nachrichten')
                ->assertSee($message->subject)
                ->click('.order-message-menu')
                ->clickLink('Verschieben')
                ->waitForText('Nachricht zuordnen')
                ->waitUntilMissing('#dataTableBuilder_processing')
                ->radio('orderid', $order->id)
                ->click('#save-assign-order-message')
                ->waitForText('Nachricht verschoben')
                ->assertSee($order->internal_order_number)
            ;

            $message->refresh();
            $this->assertEquals($order->id, $message->order_id);

            $this->assertEquals(1, $order->messages()->count());
        });
    }
}