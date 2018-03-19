<?php

namespace Mss\Http\Controllers;


use Mss\DataTables\ToOrderDataTable;

class DashboardController extends Controller
{
    public function index(ToOrderDataTable $toOrderDataTable) {
        return $toOrderDataTable->render('dashboard');
    }
}
