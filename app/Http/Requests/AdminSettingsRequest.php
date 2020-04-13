<?php

namespace Mss\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return Auth::user()->can('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'smtp.host' => 'string,nullable',
            'smtp.post' => 'string,nullable',
            'smtp.username' => 'string,nullable',
            'smtp.password' => 'string,nullable',
            'smtp.encryption' => ['string', 'nullable', Rule::in(['null', 'tls', 'ssl'])],
            'smtp.from_address' => 'email',
            'smtp.from_name' => 'string,nullable',

            'imap.host' => 'string,nullable',
            'imap.post' => 'string,nullable',
            'imap.username' => 'string,nullable',
            'imap.password' => 'string,nullable',
            'imap.encryption' => ['string', 'nullable', Rule::in(['null', 'tls', 'ssl'])],
        ];
    }
}
