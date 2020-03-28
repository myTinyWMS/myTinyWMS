<?php

namespace Mss\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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
            'email' => [
                'required',
                Rule::unique('users')->ignore($this->user)
            ],
            'username' => [
                'required',
                Rule::unique('users')->ignore($this->user)
            ],
            'roles' => 'array|exists:roles,id'
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
