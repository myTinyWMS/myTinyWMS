<?php

namespace Mss\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Mss\Models\ArticleQuantityChangelog;
use Route;

class ChangeArticleQuantityRequest extends FormRequest
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
            'changelogChangeType' => [
                'required',
                Rule::in(['add', 'sub']),
                'changelogtype'
            ],
            'changelogChange' => 'required|integer|min:1',
            'changelogType' => [
                'required',
                Rule::in([ArticleQuantityChangelog::TYPE_INCOMING, ArticleQuantityChangelog::TYPE_OUTGOING, ArticleQuantityChangelog::TYPE_INVENTORY, ArticleQuantityChangelog::TYPE_REPLACEMENT_DELIVERY, ArticleQuantityChangelog::TYPE_OUTSOURCING, ArticleQuantityChangelog::TYPE_SALE_TO_THIRD_PARTIES])
            ],
        ];
    }

    public function attributes() {
        return [
            'name' => 'Name',
        ];
    }
}
