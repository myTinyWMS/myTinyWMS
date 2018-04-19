<?php

namespace Mss\Http\Controllers;

use Carbon\Carbon;
use Mss\Services\InventoryService;

class InventoryController extends Controller
{
    public function generate() {
        $date = Carbon::now();
        return InventoryService::generatePdf($date)->download('inventur_'.$date->format('Y-m-d').'.pdf');
    }
}
