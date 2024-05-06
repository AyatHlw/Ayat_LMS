<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignUpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:4',
            'email' => 'required|string|email|unique:users',
            'role' => 'required|string',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'required|min:8|confirmed'
        ];
    }
}
