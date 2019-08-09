<?php

namespace Tests\Browser;

use Mss\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class LoginTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @return void
     * @throws \Throwable
     */
    public function test_login()
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'admin@example.com')->first();

            $browser
                ->visit('/login')
                ->assertSee('Benutzername')
                ->type('login', $user->email)
                ->type('password', 'password')
                ->click('button[type=submit]')
                ->waitForText('Dashboard');
        });
    }
}
