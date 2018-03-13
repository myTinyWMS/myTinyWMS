<?php

namespace Mss\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Mss\Models\OrderMessage;

class GlobalComposer {

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (Auth::check()) {
            $view->with('notifications', []/*Auth::user()->unreadNotifications*/);
            $view->with('unreadMessages', OrderMessage::unread()->count());
        }
    }

}