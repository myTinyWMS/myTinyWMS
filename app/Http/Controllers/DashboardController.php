<?php

namespace Mss\Http\Controllers;

use Illuminate\Http\Request;
use Mss\DataTables\ArticleDataTable;
use Mss\Models\Article;
use Mss\Models\Category;
use Mss\Models\Supplier;

class DashboardController extends Controller
{
    public function index(ArticleDataTable $articleDataTable) {
        $categories = Category::all();
        $supplier = Supplier::all();

        return $articleDataTable->render('dashboard', compact('categories', 'supplier'));
    }
}
