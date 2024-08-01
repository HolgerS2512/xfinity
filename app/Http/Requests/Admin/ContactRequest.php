<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\JsonResponseRequest;

class ContactRequest extends JsonResponseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {
        return [
            'salutation' => 'string|regex:/^[mdw]$/',
            'firstname' => 'required|string|max:60|min:2',
            'lastname' => 'required|string|max:40|min:2',
            'email' => 'required|email',
            'phone' => 'min:8|max:11|regex:/^[\+0]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/',
            'message' => 'required|min:8|max:1000',
        ];
    }
}
