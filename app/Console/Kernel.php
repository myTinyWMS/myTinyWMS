<?php

namespace Mss\Console;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Mss\Console\Commands\ImportCommand;
use Mss\Console\Commands\ImportMailsCommand;
use Mss\Console\Commands\SendInventoryMailCommand;
use Mss\Console\Commands\SetArticleNumbersCommand;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ImportCommand::class,
        SetArticleNumbersCommand::class,
        ImportMailsCommand::class,
        SendInventoryMailCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('import:mails')->everyFiveMinutes();
        $schedule->command('send:inventory')->dailyAt('07:00')->when(function () {
            return Carbon::now()->firstOfMonth()->isToday();
        });
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
