<?php

namespace Mss\Console\Commands;

use Illuminate\Console\Command;
use Mss\Services\ImportFromOnpService;
use Mss\Models\Legacy\Material as LegacyArticle;
use Mss\Models\Legacy\Category as LegacyCategory;
use Mss\Models\Legacy\Supplier as LegacySupplier;
use Mss\Models\Legacy\MaterialLog as LegacyArticleLog;

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
        $this->info('Importing Categories');
        $bar = $this->output->createProgressBar(LegacyCategory::count());
        $service->importCategories($bar);
        $bar->finish();
        $this->info(PHP_EOL);

        // import supplier
        $this->info('Importing Suppliers');
        $bar = $this->output->createProgressBar(LegacySupplier::count());
        $service->importSuppliers($bar);
        $bar->finish();
        $this->info(PHP_EOL);

        // import articles
        $this->info('Importing Articles');
        $bar = $this->output->createProgressBar(LegacyArticle::count());
        $service->importArticles($bar);
        $bar->finish();
        $this->info(PHP_EOL);

        // import log
        $this->info('Importing Article Log');
        $bar = $this->output->createProgressBar(LegacyArticleLog::count());
        $service->importLog($bar);
        $bar->finish();
        $this->info(PHP_EOL);
    }
}
