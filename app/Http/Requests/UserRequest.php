<?php

namespace Mss\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Route;

class UserRequest extends FormRequest
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
            'name' => 'required',
            'email' => 'unique:Mss\Models\User,email',
            'username' => 'unique:Mss\Models\User,username'
        ];
    }

    public function attributes() {
        return [
            'name' => __('Name'),
            'email' => __('E-Mail'),
            'username' => __('Benutzername')
        ];
    }
}
