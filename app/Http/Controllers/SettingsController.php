<?php

namespace Mss\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mss\Events\DeliverySaved;
use Mss\Models\Delivery;
use Mss\Models\User;
use Mss\Models\UserSettings;

class SettingsController extends Controller
{
    public function show() {
        event(new DeliverySaved(Delivery::find(17)));
        $settings = Auth::user()->settings();

        return view('settings.form', compact('settings'));
    }

    public function save(Request $request) {
        Auth::user()->settings()->merge($request->get('setting'));
        flash('Einstellung gespeichert', 'success');

        return response()->redirectToRoute('settings.show');
    }
}
