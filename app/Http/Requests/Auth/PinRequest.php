<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\JsonResponseRequest;

class PinRequest extends JsonResponseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
        ];
    }
}
