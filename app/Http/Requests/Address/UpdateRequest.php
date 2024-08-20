<?php

namespace App\Http\Requests\Address;

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
            'firstname' => 'required|string|max:60|min:2',
            'lastname' => 'required|string|max:40|min:2',
            'street' => 'required|string|min:3|max:255|regex:/^([A-Za-zäöüÄÖÜß.\-\s]+)\s+(\d{1,4}[a-zA-Z]?(?:[-\s]?\d{1,4}[a-zA-Z]?)?)$/',
            'details' => 'nullable|max:255|string',
            'zip' => 'required|max:20|regex:/^[A-Za-z0-9]*$/',
            'city' => 'required|min:1|max:80|string',
            'state' => 'nullable|max:50|string',
            'country' => 'required|regex:/^[A-Z]{2}$/',
            'phone' => 'nullable|max:50|regex:/^[0-9+ ]*$/',
            'active' => 'boolean',
        ];
    }
}
