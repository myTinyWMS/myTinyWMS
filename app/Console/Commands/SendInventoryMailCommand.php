<?php

namespace Mss\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Mss\Mail\InventoryMail;
use Mss\Services\InventoryService;

class SendInventoryMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:inventory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Inventory Mail';

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
        $date = Carbon::now();
        $excel = InventoryService::generateExcel($date);

        Mail::to('mail@example.com')->cc('mail@example.com')->cc('mail@example.com')->send(new InventoryMail($date, $excel, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'inventory.xlsx'));
    }
}
