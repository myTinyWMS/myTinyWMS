<?php

namespace Mss\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Mss\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('/login');
    }

    public function username()
    {
        $login = request()->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        Config::set('ldap_auth.identifiers.database.username_column', $field);
        if ($field == 'username') {
            Config::set('ldap_auth.identifiers.ldap.locate_users_by', 'samaccountname');
        } else {
            Config::set('ldap_auth.identifiers.ldap.bind_users_by', 'userprincipalname');
        }

        request()->merge([$field => $login]);
        return $field;
    }
}
