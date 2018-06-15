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
    protected $signature = 'send:inventory {date?}';

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
        if (!empty($this->argument('date'))) {
            $date = Carbon::parse($this->argument('date'));
            $to = 'mail@example.com';
            $cc = [];
        } else {
            $date = Carbon::now();
            $to = 'mail@example.com';
            $cc = ['mail@example.com', 'mail@example.com'];
        }

        $excelFilePath = InventoryService::generateExcel($date);

        Mail::to($to)->cc($cc)->send(new InventoryMail($date, file_get_contents($excelFilePath), 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', basename($excelFilePath)));
    }
}
