<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'slug'             => $this->slug,
            'excerpt'          => $this->excerpt,
            'content'          => $this->content,
            'status'           => $this->status,
            'cover_image_url'  => $this->cover_image_url,
            'tags'             => $this->tags,
            'views_count'      => $this->views_count,
            'likes_count'      => $this->likes_count,
            'reading_time'     => $this->reading_time,
            'is_featured'      => $this->is_featured,
            'is_liked'         => $this->is_liked,
            'published_at'     => $this->published_at?->toIso8601String(),
            'author'           => [
                'id'     => $this->author?->id,
                'name'   => $this->author?->name,
                'avatar' => $this->author?->avatar,
            ],
            'category'         => [
                'id'    => $this->category?->id,
                'name'  => $this->category?->name,
                'slug'  => $this->category?->slug,
                'color' => $this->category?->color,
            ],
        ];
    }
}
