<?php

namespace App\Http\Requests\ProfileSetting;

use App\Http\Requests\JsonResponseRequest;

class UpdateRequest extends JsonResponseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'newsletter_subscriber' => 'required|boolean',
        ];
    }
}
