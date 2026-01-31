<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class BatchDeletePostRequest extends FormRequest
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
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:posts,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'ids.required' => 'Veuillez sélectionner au moins un article.',
            'ids.min' => 'Veuillez sélectionner au moins un article.',
            'ids.*.exists' => 'Un des articles sélectionnés n\'existe pas.',
        ];
    }
}
