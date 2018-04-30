<?php

namespace Mss\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Route;

class NewArticleRequest extends FormRequest
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
            'quantity' => 'required|integer',
            'supplier_id' => 'required|exists:suppliers,id',
        ];
    }

    public function attributes() {
        return [
            'name' => 'Name',
            'supplier_id' => 'Lieferant',
            'quantity' => 'Bestand',
        ];
    }
}
