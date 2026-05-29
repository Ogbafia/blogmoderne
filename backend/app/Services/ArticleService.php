<?php

namespace App\Services;

use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ArticleService
{
    /**
     * Créer un nouvel article.
     */
    public function create(array $data, User $author): Article
    {
        $data['author_id'] = $author->id;

        // Gérer l'image de couverture
        if (! empty($data['cover_image_id'])) {
            $media = \App\Models\Media::findOrFail($data['cover_image_id']);
            $data['cover_image'] = $media->path;
        }
        unset($data['cover_image_id']);

        // Si publish_now, mettre le statut publié directement
        if (! empty($data['publish_now'])) {
            $data['status']       = 'published';
            $data['published_at'] = now();
        }
        unset($data['publish_now']);

        $article = Article::create($data);

        // Invalider les caches
        $this->clearArticleCache();

        return $article->load(['author', 'category']);
    }

    /**
     * Mettre à jour un article.
     */
    public function update(Article $article, array $data): Article
    {
        if (! empty($data['cover_image_id'])) {
            $media = \App\Models\Media::findOrFail($data['cover_image_id']);
            $data['cover_image'] = $media->path;
        }
        unset($data['cover_image_id']);

        $article->update($data);
        $this->clearArticleCache();

        return $article->fresh(['author', 'category']);
    }

    /**
     * Vider les caches d'articles.
     */
    public function clearArticleCache(): void
    {
        // En prod avec Redis on peut utiliser des tags
        Cache::flush(); // simplifié ; en prod utiliser Cache::tags(['articles'])->flush()
    }

    /**
     * Statistiques pour le dashboard admin.
     */
    public function getStats(): array
    {
        return Cache::remember('admin_stats', 300, function () {
            return [
                'articles' => [
                    'total'     => Article::count(),
                    'published' => Article::where('status', 'published')->count(),
                    'draft'     => Article::where('status', 'draft')->count(),
                ],
                'comments'    => \App\Models\Comment::count(),
                'subscribers' => \App\Models\NewsletterSubscriber::where('is_active', true)->count(),
                'views_total' => Article::sum('views_count'),
                'top_articles' => Article::published()
                    ->orderBy('views_count', 'desc')
                    ->limit(5)
                    ->get(['id', 'title', 'slug', 'views_count', 'likes_count']),
            ];
        });
    }
}
