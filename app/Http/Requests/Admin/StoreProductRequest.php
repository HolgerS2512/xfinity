<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\JsonResponseRequest;

class StoreProductRequest extends JsonResponseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'sku' => 'required|min:0|max:40',
            'brand' => 'string|min:0|max:80',
            'manufacturer_id' => 'integer',

            'translations' => 'required|array',

            // inner translations array
            'translations.*.locale' => 'required|string|in:en,de',
            'translations.*.name' => 'required|string|max:60|min:3',
            'translations.*.description' => 'string|max:1000',
        ];
    }
}
