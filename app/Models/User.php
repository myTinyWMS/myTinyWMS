<?php

namespace Mss\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Contracts\UserResolver;

class User extends Authenticatable implements Auditable, UserResolver
{
    use Notifiable, \OwenIt\Auditing\Auditable;

    /**
     * {@inheritdoc}
     */
    public static function resolveId() {
        return Auth::check() ? Auth::user()->getAuthIdentifier() : null;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
