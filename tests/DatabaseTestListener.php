<?php

namespace Tests;

use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use PHPUnit\Framework\TestSuite;

class DatabaseTestListener implements TestListener
{
    use TestListenerDefaultImplementation;

    /**
     * Set up the database for testing.
     *
     * @param TestSuite $suite
     */
    public function startTestSuite(TestSuite $suite): void {
        chdir(__DIR__ . '/..');

        shell_exec('php artisan create:testdb');
        dump('db created'); // Log doesn't work here
    }
}