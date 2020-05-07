<?php

namespace Mss\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Mss\Http\Controllers\Controller;
use Mss\Models\User;

class ApiController extends Controller {

    /**
     * @param mixed $ability
     * @param array $arguments
     * @return \Illuminate\Auth\Access\Response|void
     */
    public function authorize($ability, $arguments = []) {
        if (!Auth::user()->tokenCan($ability)) {
            abort(
                response()->json(['message' => 'Not allowed'], 403)
            );
        }
    }

}