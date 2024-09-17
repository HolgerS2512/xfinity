<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\JsonResponseRequest;

class StoreCategoryRequest extends JsonResponseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'level' => 'integer|min:0|max:255',
            'ranking' => 'integer',
            'parent_id' => 'integer',
            'translations' => 'required|array',

            // inner translations array
            'translations.*.locale' => 'required|string|in:en,de',
            'translations.*.name' => 'required|string|max:60|min:3',
            'translations.*.description' => 'string|max:1000',
        ];
    }
}
