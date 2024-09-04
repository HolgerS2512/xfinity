<?php

namespace App\Http\Requests\Cookie;

use App\Http\Requests\JsonResponseRequest;

class StoreRequest extends JsonResponseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "necessary" => 'required|boolean',
            "preferences" => 'required|boolean',
            "statistics" => 'required|boolean',
            "marketing" => 'required|boolean',
            "unclassified" => 'required|boolean',
        ];
    }
}
