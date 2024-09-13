<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\JsonResponseRequest;

class ChangePasswordEditRequest extends JsonResponseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'current_password' => 'required|min:8|max:255',
            'password' => 'required|string|min:8|max:255|confirmed|regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.* ).{8,255}$/',
            'password_confirmation' => 'required|string|min:8|max:255|regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.* ).{8,255}$/',
        ];
    }
}
