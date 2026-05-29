<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Article extends Model
{
    use HasUuids, SoftDeletes, HasFactory, HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'author_id',
        'category_id',
        'cover_image',
        'tags',
        'views_count',
        'likes_count',
        'reading_time',
        'is_featured',
        'published_at',
    ];

    protected $casts = [
        'tags'         => 'array',
        'is_featured'  => 'boolean',
        'published_at' => 'datetime',
        'views_count'  => 'integer',
        'likes_count'  => 'integer',
        'reading_time' => 'integer',
    ];

    protected $appends = ['cover_image_url', 'is_liked'];

    // ─── Slug ─────────────────────────────────────────────────────────────
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(100);
    }

    // ─── Relations ────────────────────────────────────────────────────────
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function likedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'article_likes')
                    ->withTimestamps();
    }

    // ─── Accesseurs ───────────────────────────────────────────────────────
    public function getCoverImageUrlAttribute(): ?string
    {
        if (!$this->cover_image) {
            return null;
        }

        // Si c'est un SVG, le convertir en base64 pour l'embarquer dans la réponse
        if (str_ends_with($this->cover_image, '.svg')) {
            $filePath = storage_path('app/public/' . $this->cover_image);
            if (file_exists($filePath)) {
                $svgContent = file_get_contents($filePath);
                $base64 = base64_encode($svgContent);
                return 'data:image/svg+xml;base64,' . $base64;
            }
        }

        // Pour les PNG et autres, retourner l'URL publique
        return asset('storage/' . $this->cover_image);
    }

    public function getIsLikedAttribute(): bool
    {
        if (! auth()->check()) {
            return false;
        }
        return $this->likedBy()->where('user_id', auth()->id())->exists();
    }

    // ─── Scopes ───────────────────────────────────────────────────────────
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory(Builder $query, string $slug): Builder
    {
        return $query->whereHas('category', fn ($q) => $q->where('slug', $slug));
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->whereFulltext(['title', 'content', 'excerpt'], $term);
    }

    // ─── Méthodes métier ──────────────────────────────────────────────────
    public function publish(): void
    {
        $this->update([
            'status'       => 'published',
            'published_at' => now(),
        ]);
    }

    public function unpublish(): void
    {
        $this->update(['status' => 'draft']);
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function calculateReadingTime(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, (int) ceil($wordCount / 200)); // 200 mots/min
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Article $article) {
            if ($article->isDirty('content')) {
                $article->reading_time = $article->calculateReadingTime();
            }
        });
    }
}
