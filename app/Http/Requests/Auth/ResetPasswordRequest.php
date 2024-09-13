<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\JsonResponseRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends JsonResponseRequest
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
