<?php

namespace Mss\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Mss\Models\UserSettings;
use Illuminate\Support\Facades\Auth;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $locale = Auth::user()->settings()->get(UserSettings::SETTINGS_LANGUAGE) ?? 'de';

        app()->setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
