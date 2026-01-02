<?php

namespace Mss\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Mss\Mail\LowQuantities;
use Mss\Models\Article;

class NotifyLowQuantitiesCommand extends Command {
    protected $signature = 'notify:low-quantities';

    protected $description = 'Command description';

    public function handle(): void {
        $items = Article::where('min_quantity', '>', 0)
            ->whereRaw('quantity < min_quantity')
            ->where('status', Article::STATUS_ACTIVE)
            ->get();

        $to = explode(',', env('INVENTORY_MANUAL_RECEIVER'));
        Mail::to($to)->send(new LowQuantities($items));
    }
}
