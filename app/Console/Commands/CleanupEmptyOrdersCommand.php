<?php

namespace Mss\Console\Commands;

use Carbon\Carbon;
use Mss\Models\Order;
use Illuminate\Console\Command;

class CleanupEmptyOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emptyorders:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes order without a supplier';

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
        Order::whereNull('supplier_id')->where('created_at', '<', Carbon::now()->subHour()->format('Y-m-d H:i:s'))->delete();
    }
}
