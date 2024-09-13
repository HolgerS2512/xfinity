<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\JsonResponseRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends JsonResponseRequest
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
            'email' => 'required|email|unique:users,email',
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
