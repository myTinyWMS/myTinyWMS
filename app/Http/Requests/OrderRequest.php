<?php

namespace Mss\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Route;

class OrderRequest extends FormRequest
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
            'order_id' => 'exists:orders,id',
            'supplier' => 'exists:suppliers,id',
            'total_cost' => 'nullable|regex:/[0-9]+[.,]?[0-9]*/',
            'shipping_cost' => 'nullable|regex:/[0-9]+[.,]?[0-9]*/',
            'order_date' => 'nullable|date',
            'expected_delivery' => 'nullable|date',
            'article.*' => 'nullable|exists:articles,id',
            'quantity.*' => 'nullable|integer',
            'price.*' => 'nullable|regex:/[0-9]+[.,]?[0-9]*/'
        ];
    }

    public function attributes() {
        return [
            'supplier' => 'Lieferant',
            'total_cost' => 'Gesamtkosten',
            'shipping_cost' => 'Lieferkosten',
            'order_date' => 'Bestelldatum',
            'expected_delivery' => 'Liefertermin',
            'article.*' => 'Artikel',
            'quantity.*' => 'Menge',
            'price.*' => 'Preis',
        ];
    }
}
