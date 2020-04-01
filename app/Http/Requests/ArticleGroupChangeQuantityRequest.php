<?php

namespace Mss\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Mss\Models\ArticleQuantityChangelog;

class ArticleGroupChangeQuantityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return Auth::user()->can('article-group.change_quantity');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'changelogChangeType' => [
                'required',
                Rule::in(['add', 'sub']),
                'changelogtype'
            ],
            'quantity.*' => 'required|integer|min:0',
            'changelogType' => [
                'required',
                Rule::in(ArticleQuantityChangelog::getAvailableTypes())
            ],
        ];
    }
}
