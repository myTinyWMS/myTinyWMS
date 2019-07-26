<?php

namespace Tests;

use GuzzleHttp\Client;
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

    protected function startMailTest() {
        // Instantiating GuzzleHTTP client
        $client = new Client();
        // Deleting all the emails, so that inbox would be empty
        $client->delete('http://mailhog:8025/api/v1/messages');
    }

    protected function assertMailsSent($mailCount) {
        $client = new Client();

        // sending request to get all emails
        $response = $client->get('http://mailhog:8025/api/v1/messages');
        $body = $response->getBody();
        $receivedMailCount = collect(json_decode($body, true))->count();
        dump(collect(json_decode($body, true)), $receivedMailCount);

        $this->assertEquals($mailCount, $receivedMailCount);
    }
}
