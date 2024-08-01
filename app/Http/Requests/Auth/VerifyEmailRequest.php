<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\JsonResponseRequest;

class VerifyEmailRequest extends JsonResponseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'pin' => 'required|integer|max:100000|min:10',
        ];
    }
}
