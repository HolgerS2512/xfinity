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
            'article_number' => 'required|string|min:0|max:255',
            'ranking' => 'integer',
            'name' => 'required|string|max:60|min:3',
            'description' => 'string|max:1000',
            'stock' => 'integer',
            'category_id' => 'required|integer',
        ];
    }
}
