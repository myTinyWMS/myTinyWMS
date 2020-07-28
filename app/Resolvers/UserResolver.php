<?php
namespace Mss\Resolvers;

use Illuminate\Support\Facades\Auth;

class UserResolver implements \OwenIt\Auditing\Contracts\UserResolver
{
    /**
     * {@inheritdoc}
     */
    public static function resolve() {
        if (Auth::check()) {
            return Auth::user();
        }
    }
}