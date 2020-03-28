<?php

namespace Mss\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Mss\Http\Controllers\Controller;

class StartController extends Controller
{
    public function index() {
        return view('admin.index');
    }
}
