<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'string|max:255',
            'content' => 'string',
            'category_id' => 'exists:categories,id',
            'excerpt' => 'nullable|string',
            'cover_image_id' => 'nullable|uuid|exists:media,id',
            'tags' => 'nullable|array',
        ];
    }
}
