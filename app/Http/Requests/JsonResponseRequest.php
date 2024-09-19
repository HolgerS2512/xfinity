<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;

class JsonResponseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return \Illuminate\Http\Response
     */
    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();

        Log::warning('Validation failed', [
            'errors' => $errors,
            'request_data' => $this->all(),
        ]);

        throw new HttpResponseException(response()->json([
            'status'    => false,
            'validator' => $validator->errors(),
        ], 400));
    }
}
