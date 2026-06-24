<?php

namespace Mss\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
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
            'cost_center' => 'required'
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('name')) {
            $this->merge([
                'name' => trim(strip_tags($this->input('name'))),
            ]);
        }
    }

    public function attributes() {
        return [
            'name' => __('Name'),
            'cost_center' => __('Kostenstelle')
        ];
    }
}
