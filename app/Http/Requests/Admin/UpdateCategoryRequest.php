<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\JsonResponseRequest;

class UpdateCategoryRequest extends JsonResponseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'new_ranking' => 'integer|min:1',
            'name' => 'string|max:60|min:3',
        ];
    }
}
