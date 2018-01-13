<?php

namespace Mss\Http\Controllers;

use Illuminate\Http\Request;
use Mss\DataTables\ArticleDataTable;

class DashboardController extends Controller
{
    public function index(ArticleDataTable $articleDataTable) {
        return $articleDataTable->render('dashboard');
    }
}
