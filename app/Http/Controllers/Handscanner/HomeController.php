<?php

namespace Mss\Http\Controllers\Handscanner;

use Mss\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index() {
        return view('handscanner.index');
    }
}
