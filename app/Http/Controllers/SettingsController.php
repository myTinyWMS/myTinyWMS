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
        $settings = Auth::user()->settings();
        $signature = html_entity_decode(Auth::user()->signature);

        return view('settings.form', compact('settings', 'signature'));
    }

    public function save(Request $request) {
        if (is_array($request->get('setting'))) {
            Auth::user()->settings()->merge($request->get('setting'));
        }

        $user = Auth::user();
        $user->signature = htmlentities($request->get('signature'));
        $user->save();

        flash('Einstellung gespeichert', 'success');

        return response()->redirectToRoute('settings.show');
    }
}
