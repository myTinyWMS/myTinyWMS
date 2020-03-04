<?php

namespace Mss\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Mss\Notifications\ResetPassword;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Contracts\UserResolver;

class User extends Authenticatable implements Auditable, UserResolver
{
    use Notifiable, \OwenIt\Auditing\Auditable;

    public static function getFieldNames() {
        return [];
    }

    public static function getAuditName() {
        return __('Benutzer');
    }

    /**
     * {@inheritdoc}
     */
    public static function resolve() {
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

    protected $casts = [
        'settings' => 'json'
    ];

    public function notes() {
        return $this->hasMany(ArticleNote::class);
    }

    public function articleQuantityChangelogs() {
        return $this->hasMany(ArticleQuantityChangelog::class);
    }

    /**
     * @return UserSettings
     */
    public function settings() {
        return new UserSettings($this);
    }

    public function sendPasswordResetNotification($token) {
        $this->notify(new ResetPassword($token));
    }
}