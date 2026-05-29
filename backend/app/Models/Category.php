<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
    use HasSlug, HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'color', 'image'];

    protected $appends = ['image_url', 'articles_count'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function getArticlesCountAttribute(): int
    {
        return $this->articles()->published()->count();
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        if (str_ends_with($this->image, '.svg')) {
            $filePath = storage_path('app/public/' . $this->image);
            if (file_exists($filePath)) {
                $svgContent = file_get_contents($filePath);
                return 'data:image/svg+xml;base64,' . base64_encode($svgContent);
            }
        }

        return asset('storage/' . $this->image);
    }
}
