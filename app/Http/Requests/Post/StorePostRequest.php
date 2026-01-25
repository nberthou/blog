<?php

namespace App\Http\Requests\Post;

use App\Enums\PostStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['required', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'status' => ['sometimes', Rule::enum(PostStatus::class)],
            'published_at' => ['nullable', 'date'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,png,webp,gif', 'max:5120'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'excerpt.required' => 'L\'extrait est obligatoire.',
            'excerpt.max' => 'L\'extrait ne peut pas dépasser 500 caractères.',
            'content.required' => 'Le contenu est obligatoire.',
            'status.enum' => 'Le statut sélectionné n\'est pas valide.',
            'published_at.date' => 'La date de publication n\'est pas valide.',
            'featured_image.image' => 'Le fichier doit être une image.',
            'featured_image.mimes' => 'L\'image doit être au format JPEG, PNG, WebP ou GIF.',
            'featured_image.max' => 'L\'image ne peut pas dépasser 5 Mo.',
        ];
    }
}
