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
        DB::statement("drop database if exists mss_test");
        DB::statement("create database mss_test");

        DB::purge('testdb');

        Artisan::call('migrate', ['--database' => 'testdb']);
        Artisan::call('db:seed', ['--database' => 'testdb']);
        Artisan::call('articlenumbers:set', ['--database' => 'testdb', '--force' => true]);
    }
}
