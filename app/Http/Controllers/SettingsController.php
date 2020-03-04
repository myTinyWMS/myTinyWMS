<?php

namespace Mss\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Mss\Events\DeliverySaved;
use Mss\Http\Requests\ChangePasswordRequest;
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

        flash(__('Einstellung gespeichert'), 'success');

        return response()->redirectToRoute('settings.show');
    }

    public function changePwForm() {
        return view('settings.change_pw_form');
    }

    public function changePw(ChangePasswordRequest $request) {
        if (!Hash::check($request->get('old_pw'), Auth::user()->password)) {
            flash(__('Das alte Passwort ist falsch'), 'danger');
            return response()->redirectToRoute('settings.change_pw');
        }

        $user = Auth::user();
        $user->password = Hash::make($request->get('new_pw'));
        $user->save();

        flash(__('Passwort geändert'), 'success');
        return response()->redirectToRoute('settings.change_pw');
    }
}
