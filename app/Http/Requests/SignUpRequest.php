<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignUpRequest extends FormRequest
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
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'sex' => 'required|string',
            'dob' => 'required|date_format:Y-m-d',
            'password' => 'required|string',
            'email' => 'required|string|unique:users|email',
        ];
    }
}
