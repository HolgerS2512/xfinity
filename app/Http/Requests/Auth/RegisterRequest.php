<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\JsonResponseRequest;

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
            'password' => 'required|string|min:8|max:255|regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.* ).{8,255}$/'
        ];
    }
}
