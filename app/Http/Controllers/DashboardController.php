<?php

namespace Mss\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Mss\DataTables\ToOrderDataTable;

class DashboardController extends Controller
{
    public function index(ToOrderDataTable $toOrderDataTable) {
        if (!Auth::check()) {
            return response()->redirectToRoute('login');
        }

        return $toOrderDataTable->render('dashboard');
    }
}
