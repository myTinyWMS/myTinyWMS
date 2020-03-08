<?php

namespace Mss\Services;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Mss\Models\OrderMessage;

class GlobalPageService {

    public function getNotifications() {
        return Auth::check() ? Auth::user()->unreadNotifications : [];
    }

    public function getUnreadMessageCount() {
        if (!Auth::check()) return 0;

        return Cache::remember('global.getUnreadMessageCount', 10, function () {
            return OrderMessage::unread()->count();
        });
    }

    public function hasMiniNavbar() {
        return isset($_COOKIE['mini-navbar']);
    }

}