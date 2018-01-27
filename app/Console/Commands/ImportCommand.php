<?php

namespace Mss\Console\Commands;

use Illuminate\Console\Command;
use Mss\Services\ImportFromOnpService;

class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Data from ONP Database';

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
        $service = new ImportFromOnpService($this);
        // import categories
//        $service->importCategories();

        // import supplier
//        $service->importSuppliers();

        // import articles
        $service->importArticles();

        // import log
    }
}
