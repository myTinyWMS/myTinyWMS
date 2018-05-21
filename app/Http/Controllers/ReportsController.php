<?php

namespace Mss\Http\Controllers;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Mss\Services\InventoryService;

class ReportsController extends Controller
{
    public function index() {
        return view('reports.index');
    }

    public function generateInventoryPdf() {
        $date = Carbon::now();
        return InventoryService::generatePdf($date)->download('inventur_'.$date->format('Y-m-d').'.pdf');
    }

    public function generateInventoryReport(Request $request) {
        return response()->streamDownload(function () use ($request) {
            echo InventoryService::generateReport($request->get('month'));
        }, 'InventurReport.xlsx', ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
}
