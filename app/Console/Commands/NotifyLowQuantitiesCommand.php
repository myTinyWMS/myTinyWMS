<?php

namespace Mss\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Mss\DataTables\ToOrderDataTable;
use Mss\Mail\LowQuantities;
use Mss\Models\Article;

class NotifyLowQuantitiesCommand extends Command {
    protected $signature = 'notify:low-quantities';

    protected $description = 'Command description';

    public function handle(): void {
        $query = (new ToOrderDataTable())->query(new Article());
        $items = $query->get();

        $to = explode(',', env('INVENTORY_MANUAL_RECEIVER'));
        Mail::to($to)->send(new LowQuantities($items));
    }
}
