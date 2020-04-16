<?php

namespace Mss\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Mss\Models\User;
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
                Rule::requiredIf(function () {
                    if (request()->user && $user = User::find(request()->user)) {
                        return $user->getSource() == User::SOURCE_LOCAL;
                    }
                }),
                Rule::unique('users')->ignore($this->user)
            ],
            'username' => [
                Rule::requiredIf(function () {
                    if (request()->user && $user = User::find(request()->user)) {
                        return $user->getSource() == User::SOURCE_LOCAL;
                    }
                }),
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
