<?php

use duncan3dc\Laravel\Dusk;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

require __DIR__ . "/vendor/autoload.php";

class MyChrome extends \duncan3dc\Laravel\Drivers\Chrome {
	public function getDriver()
	{
		return RemoteWebDriver::create("http://chrome:4444/wd/hub", DesiredCapabilities::chrome());
	}
}

$dusk = new Dusk(new MyChrome());

$dusk->getBrowser()->visit('http://www.overnightprints.de')->assertSee('Impressum');
var_dump($dusk->getBrowser()->text('#mainpromo-discountcode'));