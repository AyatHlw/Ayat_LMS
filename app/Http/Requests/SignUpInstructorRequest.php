<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignUpInstructorRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:4',
            'email' => 'required|string|email|unique:users',
            'role' => 'required|string',
            //'CV' => 'required|file|mimes:pdf|max:3072',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'required|min:8|confirmed'
        ];
    }
}