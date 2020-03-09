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
            'order_id' => 'required|exists:orders,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'total_cost' => 'nullable|regex:/[0-9]+[.,]?[0-9]*/',
            'shipping_cost' => 'nullable|regex:/[0-9]+[.,]?[0-9]*/',
            'order_date' => 'nullable|date',
            'expected_delivery.*' => 'nullable|date',
            'article_data.*.id' => 'required|exists:articles,id',
            'article_data.*.quantity' => 'required|integer',
            'article_data.*.price' => 'required|regex:/[0-9]+[.,]?[0-9]*/'
        ];
    }

    public function attributes() {
        return [
            'supplier' => __('Lieferant'),
            'total_cost' => __('Gesamtkosten'),
            'shipping_cost' => __('Lieferkosten'),
            'order_date' => __('Bestelldatum'),
            'expected_delivery' => __('Liefertermin'),
            'article.*' => __('Artikel'),
            'quantity.*' => __('Menge'),
            'price.*' => __('Preis'),
        ];
    }
}
