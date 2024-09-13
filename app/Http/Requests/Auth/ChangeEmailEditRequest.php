<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\JsonResponseRequest;

class ChangeEmailEditRequest extends JsonResponseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'current_email' => 'required|email',
            'email' => 'required|email|confirmed|unique:users,email',
            'email_confirmation' => 'required|email',
        ];
    }
}
