<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\JsonResponseRequest;

class ChangeEmailUpdateRequest extends JsonResponseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'pin' => 'required|integer|max:100000|min:10000',
            'current_email' => 'required|email',
            'new_email' => 'required|email|confirmed|unique:users,email',
            'new_email_confirmation' => 'required|email',
        ];
    }
}
