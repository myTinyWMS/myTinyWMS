<?php

namespace Mss\Console\Commands;

use Illuminate\Console\Command;
use Mss\Services\ImportMailsService;


class ImportMailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:mails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports Mails from Server';

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
    public function handle() {
        $service = new ImportMailsService();
        $service->process();
    }
}
