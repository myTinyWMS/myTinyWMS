<?php

namespace Tests\Browser;

use Illuminate\Support\Facades\Cache;
use Mss\Services\ConfigService;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class AdminSettingsTest extends DuskTestCase
{
    /**
     * login before all other tests
     *
     * @throws \Throwable
     */
    public function test_login() {
        Cache::flush();

        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 2000);
            $this->login($browser);
        });
    }

    public function test_setting_outgoing_email_settings() {
        $this->browse(function (Browser $browser) {
            $encryption = config('mail.encryption') == 'tls' ? 'ssl' : 'tls';

            $browser
                ->visit('/admin/settings')
                ->assertSee('Ausgehende E-Mails')
                ->type('smtp_host', 'host1')
                ->type('smtp_port', '12345')
                ->type('smtp_username', 'testuser')
                ->type('smtp_password', 'testpassword')
                ->select('smtp_encryption', $encryption)
                ->type('smtp_from_address', 'testuser@example.com')
                ->type('smtp_from_name', 'testuser')

                ->click('#saveSettings')
                ->waitForText('Einstellungen gespeichert')
                ->visit('/admin/settings')
                ->assertValue('#smtp_host', 'host1')
                ->assertValue('#smtp_port', '12345')
                ->assertValue('#smtp_username', 'testuser')
                ->assertValue('#smtp_password', 'testpassword')
                ->assertValue('#smtp_encryption', $encryption)
                ->assertValue('#smtp_from_address', 'testuser@example.com')
                ->assertValue('#smtp_from_name', 'testuser')
            ;

            // refresh config
            ConfigService::setConfigFromSettings();

            $this->assertEquals('host1', settings('smtp.host'));
            $this->assertEquals('12345', settings('smtp.port'));
            $this->assertEquals('testuser', decrypt(settings('smtp.username')));
            $this->assertEquals('testpassword', decrypt(settings('smtp.password')));
            $this->assertEquals('testuser@example.com', settings('smtp.from_address'));
            $this->assertEquals('testuser', settings('smtp.from_name'));
            $this->assertEquals($encryption, settings('smtp.encryption'));

            $this->assertEquals('host1', config('mail.host'));
            $this->assertEquals('12345', config('mail.port'));
            $this->assertEquals('testuser', config('mail.username'));
            $this->assertEquals('testpassword', config('mail.password'));
            $this->assertEquals('testuser@example.com', config('mail.from.address'));
            $this->assertEquals('testuser', config('mail.from.name'));
            $this->assertEquals($encryption, config('mail.encryption'));
        });
    }

    public function test_setting_incoming_email_settings() {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/admin/settings')
                ->assertSee('Import eingehender E-Mails für Bestellungen')
                ->click('#imap_enabled + ins')
                ->click('#imap_delete + ins')
                ->type('imap_host', 'host2')
                ->type('imap_port', '123456')
                ->type('imap_username', 'testuser1')
                ->type('imap_password', 'testpassword1')
                ->select('imap_encryption', 'tls')

                ->click('#saveSettings')
                ->waitForText('Einstellungen gespeichert')
                ->visit('/admin/settings')
                ->assertChecked('imap_enabled')
                ->assertChecked('imap_delete')
                ->assertValue('#imap_host', 'host2')
                ->assertValue('#imap_port', '123456')
                ->assertValue('#imap_username', 'testuser1')
                ->assertValue('#imap_password', 'testpassword1')
                ->assertValue('#imap_encryption', 'tls')
            ;
        });

        // refresh config
        ConfigService::setConfigFromSettings();

        $this->assertEquals('host2', settings('imap.host'));
        $this->assertEquals('123456', settings('imap.port'));
        $this->assertEquals('testuser1', decrypt(settings('imap.username')));
        $this->assertEquals('testpassword1', decrypt(settings('imap.password')));
        $this->assertEquals('tls', settings('imap.encryption'));
        $this->assertEquals(true, settings('imap.enabled'));
        $this->assertEquals(true, settings('imap.delete'));

        $this->assertEquals('host2', config('imap.accounts.default.host'));
        $this->assertEquals('123456', config('imap.accounts.default.port'));
        $this->assertEquals('testuser1', config('imap.accounts.default.username'));
        $this->assertEquals('testpassword1', config('imap.accounts.default.password'));
        $this->assertEquals('tls', config('imap.accounts.default.encryption'));
    }
}