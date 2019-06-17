<?php

namespace Tests;

use Illuminate\Support\Facades\DB;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Mss\Models\User;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {

    }

    /**
     * Boot the testing helper traits.
     *
     * @return array
     * @throws \Exception
     */
    protected function setUpTraits(): array
    {
        $uses = parent::setUpTraits();

        $result = (DB::select("select schema_name from information_schema.schemata where schema_name = 'mss_test';"));
        if (!$result || count($result) == 0) {
            dd('run php artisan create:testdb first!');
        }

        return $uses;
    }

    protected function baseUrl()
    {
        return 'http://mss.test';
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        return RemoteWebDriver::create(
            'http://selenium:4444', DesiredCapabilities::chrome()
        );
    }

    /**
     * @param Browser $browser
     * @return Browser
     */
    protected function login(Browser $browser) {
        return $browser->loginAs(User::first())
            ->visit('/reports')
            ->assertSee('Reports');
    }
}
