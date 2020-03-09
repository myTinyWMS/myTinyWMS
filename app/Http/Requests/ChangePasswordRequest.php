<?php

namespace Mss\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Route;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'old_pw' => 'required',
            'new_pw' => 'required',
            'new_pw2' => 'required|same:new_pw'
        ];
    }

    public function attributes() {
        return [
            'old_pw' => __('altes Passwort'),
            'new_pw' => __('neues Passwort'),
            'new_pw2' => __('neues Passwort Wiederholung'),
        ];
    }
}
