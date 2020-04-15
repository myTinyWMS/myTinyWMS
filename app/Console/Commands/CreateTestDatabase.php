<?php

namespace Mss\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CreateTestDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:testdb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // use dump
        DB::connection('testdb')->unprepared(file_get_contents(database_path('mss_testdb.sql')));
        Artisan::call('db:seed', ['--database' => 'testdb', '--force' => true]);
        Artisan::call('articlenumbers:set', ['--database' => 'testdb', '--force' => true]);

        settings()->set([
            'smtp.host' => 'mailhog',
            'smtp.port' => '1025',
            'smtp.username' => encrypt(null),
            'smtp.password' => encrypt(null),
            'smtp.from_address' => 'mail@example.com'
        ]);
    }
}
