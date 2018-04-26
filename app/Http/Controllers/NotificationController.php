<?php

namespace Mss\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller {

    public function delete($id) {
        Auth::user()->notifications()->where('id', $id)->delete();
    }
}