<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

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
        /*static::startChromeDriver();*/
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        return RemoteWebDriver::create(
            'http://webdriver:4445', DesiredCapabilities::phantomjs()->setCapability(WebDriverCapabilityType::ACCEPT_SSL_CERTS, true)
        );

	    /*$settings = DesiredCapabilities::chrome();

	    $options = new ChromeOptions();
	    $options->addArguments(['--window-size=1600,2000', '--disable-notifications', '--incognito']);
	    $settings->setCapability(ChromeOptions::CAPABILITY, $options);

	    return RemoteWebDriver::create(
		    'http://chrome:4444', $settings
	    );*/

    }
}
