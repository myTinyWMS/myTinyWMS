<?php

namespace Mss\Http\Controllers\Handscanner;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mss\Http\Controllers\Controller;
use Mss\Models\User;
use Mss\Models\UserSettings;

class LoginController extends Controller
{
    public function login() {
        return view('handscanner.login');
    }

    public function processLogin(Request $request) {
        $user = User::findOrFail($request->get('user'));
        if ($user->settings()->get(UserSettings::SETTINGS_HANDSCANNER_PIN_CODE) == $request->get('pin')) {
            Auth::loginUsingId($user->id);
            return response()->redirectToRoute('handscanner.index');
        }

        flash('Login ungültig', 'danger');
        return redirect()->back();
    }

    public function processLogout(Request $request) {
        Auth::guard()->logout();

        $request->session()->invalidate();

        return redirect()->route('handscanner.login');
    }
}
