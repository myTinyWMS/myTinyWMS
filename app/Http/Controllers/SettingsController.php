<?php

namespace Mss\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function show() {
        $settings = Auth::user()->settings();

        return view('settings.form', compact('settings'));
    }

    public function save(Request $request) {
        dd($request->all());
    }
}
