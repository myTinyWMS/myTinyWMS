<?php

namespace Mss\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Mss\Mail\InventoryMail;
use Mss\Models\Article;
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
            $to = explode(',', env('INVENTORY_MANUAL_RECEIVER'));
        } else {
            $date = Carbon::now();
            $to = explode(',', env('INVENTORY_AUTOMATIC_RECEIVER'));
        }

        $excelFilePath = InventoryService::generateExcel($date->copy()->subDay());
        $inventoryReportPath = InventoryService::generateReportAsFile($date->copy()->subDay()->format('Y-m'), Article::INVENTORY_TYPE_CONSUMABLES);
//        $invoicesWithoutDeliveryPath = InventoryService::generateInvoicesWithoutDeliveryReport($date);
//        $deliveriesWithoutInvoicesPath = InventoryService::generateDeliveriesWithoutInvoiceReport($date);

        $attachments = [
            [file_get_contents($excelFilePath), 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', basename($excelFilePath)],
            [file_get_contents($inventoryReportPath), 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', basename($inventoryReportPath)],
//            [file_get_contents($invoicesWithoutDeliveryPath), 'application/pdf', basename($invoicesWithoutDeliveryPath)],
//            [file_get_contents($deliveriesWithoutInvoicesPath), 'application/pdf', basename($deliveriesWithoutInvoicesPath)]
        ];

        Mail::to($to)->send(new InventoryMail($date, $attachments));
    }
}
