<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\JsonResponseRequest;

class UpdateRequest extends JsonResponseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'salutation' => 'required|string|regex:/^[mdwz]$/',
            'firstname' => 'required|string|max:60|min:2',
            'lastname' => 'required|string|max:40|min:2',
            'birthday' => 'required|date_format:Y-m-d',
        ];
    }
}
