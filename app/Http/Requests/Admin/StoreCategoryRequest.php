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
            'name' => 'required|string|max:60|min:3',
            'level' => 'integer|min:0|max:255',
            'ranking' => 'integer',
            'parent_id' => 'integer',
            'description' => 'string|max:1000',
        ];
    }
}
