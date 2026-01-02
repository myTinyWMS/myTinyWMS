<?php

namespace Mss\Console;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Mss\Console\Commands\AddRoleToUserCommand;
use Mss\Console\Commands\CalculateMinQuantitiesCommand;
use Mss\Console\Commands\CleanupEmptyOrdersCommand;
use Mss\Console\Commands\CreateTestDatabase;
use Mss\Console\Commands\ImportMailsCommand;
use Mss\Console\Commands\NotifyLowQuantitiesCommand;
use Mss\Console\Commands\SendInventoryMailCommand;
use Mss\Console\Commands\SetArticleNumbersCommand;
use Mss\Console\Commands\Version;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CreateTestDatabase::class,
        SetArticleNumbersCommand::class,
        ImportMailsCommand::class,
        SendInventoryMailCommand::class,
        CleanupEmptyOrdersCommand::class,
        AddRoleToUserCommand::class,
        Version::class,
        CalculateMinQuantitiesCommand::class,
        NotifyLowQuantitiesCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('emptyorders:clear')->hourly();

        if(settings('imap.enabled', false) && !env('APP_DEMO')) {
            $schedule->command('import:mails')->everyFiveMinutes();
        }

        if (!env('APP_DEMO')) {
            $schedule->command('send:inventory')->dailyAt('07:00')->when(function () {
                return Carbon::now()->firstOfMonth()->isToday();
            });
        }

        $schedule->command('horizon:snapshot')->everyFiveMinutes();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
