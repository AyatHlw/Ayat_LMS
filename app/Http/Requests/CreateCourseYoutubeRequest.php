<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCourseYoutubeRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'image' => 'required|image|max:5000',
            'description' => 'required|string',
            'cost' => 'required|numeric',
            'videos' => 'required|array',
            'videos.*' => [
                'required',
                'url',
                'regex:/^(https?\:\/\/)?(www\.youtube\.com|youtu\.be)\/.+$/'
            ],
            ];
    }
}
