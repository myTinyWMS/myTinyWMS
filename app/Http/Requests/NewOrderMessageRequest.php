<?php

namespace Mss\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Route;

class NewOrderMessageRequest extends FormRequest
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
            'subject' => 'required',
            'receiver' => 'required|emails',
            'body' => 'required',
        ];
    }

    public function attributes() {
        return [
            'subject' => __('Betreff'),
            'receiver' => __('Empfänger'),
            'body' => __('Nachricht')
        ];
    }
}
