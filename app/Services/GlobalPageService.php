<?php

namespace Mss\Services;


use Illuminate\Support\Facades\Auth;
use Mss\Models\OrderMessage;

class GlobalPageService {

    public function getNotifications() {
        return Auth::check() ? Auth::user()->unreadNotifications : 0;
    }

    public function getUnreadMessageCount() {
        return Auth::check() ? OrderMessage::unread()->count() : 0;
    }

    public function hasMiniNavbar() {
        return isset($_COOKIE['mini-navbar']);
    }

}