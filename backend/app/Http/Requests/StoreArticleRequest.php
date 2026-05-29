<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'excerpt' => 'nullable|string',
            'cover_image_id' => 'nullable|uuid|exists:media,id',
            'tags' => 'nullable|array',
            'publish_now' => 'nullable|boolean',
        ];
    }
}
