<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\JsonResponseRequest;
use Illuminate\Validation\Rules\Password;

class LoginRequest extends JsonResponseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => [
                'required',
                'string',
                'max:255',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
                'confirmed'
            ],
        ];
    }
}
