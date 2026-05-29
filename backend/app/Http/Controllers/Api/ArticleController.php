<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ArticleController extends Controller
{
    public function __construct(protected ArticleService $articleService) {}

    /**
     * GET /api/articles
     * Liste paginée des articles publiés avec filtres.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'page'        => 'integer|min:1',
            'per_page'    => 'integer|min:1|max:50',
            'category'    => 'string|exists:categories,slug',
            'tag'         => 'string',
            'search'      => 'string|min:2|max:100',
            'featured'    => 'boolean',
            'sort'        => 'in:latest,oldest,popular,views',
        ]);

        $cacheKey = 'articles_' . md5(json_encode($validated));

        $articles = Cache::remember($cacheKey, 300, function () use ($validated, $request) {
            $query = Article::with(['author:id,name,avatar', 'category:id,name,slug,color'])
                            ->published();

            if (! empty($validated['category'])) {
                $query->byCategory($validated['category']);
            }

            if (! empty($validated['tag'])) {
                $query->whereJsonContains('tags', $validated['tag']);
            }

            if (! empty($validated['search'])) {
                $query->search($validated['search']);
            }

            if (! empty($validated['featured'])) {
                $query->featured();
            }

            $sort = $validated['sort'] ?? 'latest';
            match ($sort) {
                'latest'  => $query->orderBy('published_at', 'desc'),
                'oldest'  => $query->orderBy('published_at', 'asc'),
                'popular' => $query->orderBy('likes_count', 'desc'),
                'views'   => $query->orderBy('views_count', 'desc'),
            };

            return $query->paginate($validated['per_page'] ?? 12);
        });

        return response()->json([
            'data'  => ArticleResource::collection($articles),
            'meta'  => [
                'total'        => $articles->total(),
                'per_page'     => $articles->perPage(),
                'current_page' => $articles->currentPage(),
                'last_page'    => $articles->lastPage(),
            ],
        ]);
    }

    /**
     * POST /api/articles (auth + admin/author)
     */
    public function store(StoreArticleRequest $request): JsonResponse
    {
        $article = $this->articleService->create(
            $request->validated(),
            $request->user()
        );

        return response()->json([
            'message' => 'Article créé avec succès.',
            'data'    => new ArticleResource($article),
        ], 201);
    }

    /**
     * GET /api/articles/{slug}
     */
    public function show(string $slug): JsonResponse
    {
        $article = Article::with([
            'author:id,name,avatar,bio',
            'category:id,name,slug,color',
            'comments.author:id,name,avatar',
            'comments.replies.author:id,name,avatar',
            'media:id,path,alt_text,width,height',
        ])->published()->where('slug', $slug)->firstOrFail();

        // Incrémenter le compteur de vues (avec throttle par IP)
        $ipKey = 'article_view_' . $article->id . '_' . request()->ip();
        if (! Cache::has($ipKey)) {
            $article->incrementViews();
            Cache::put($ipKey, true, 3600); // 1h par IP
        }

        return response()->json([
            'data' => new ArticleResource($article),
        ]);
    }

    /**
     * PUT /api/articles/{id} (auth + ownership ou admin)
     */
    public function update(UpdateArticleRequest $request, Article $article): JsonResponse
    {
        // $this->authorize('update', $article);

        $updated = $this->articleService->update($article, $request->validated());

        // Invalider le cache
        Cache::forget('articles_*');
        Cache::forget('article_' . $article->slug);

        return response()->json([
            'message' => 'Article mis à jour.',
            'data'    => new ArticleResource($updated),
        ]);
    }

    /**
     * DELETE /api/articles/{id} (auth + ownership ou admin)
     */
    public function destroy(Article $article): JsonResponse
    {
        // $this->authorize('delete', $article);

        $article->delete();

        return response()->json([
            'message' => 'Article supprimé.',
        ]);
    }

    /**
     * POST /api/articles/{id}/publish (admin)
     */
    public function publish(Article $article): JsonResponse
    {
        // $this->authorize('publish', $article);

        $article->publish();

        return response()->json([
            'message' => 'Article publié.',
            'data'    => new ArticleResource($article),
        ]);
    }

    /**
     * POST /api/articles/{id}/unpublish (admin)
     */
    public function unpublish(Article $article): JsonResponse
    {
        // $this->authorize('publish', $article);

        $article->unpublish();

        return response()->json([
            'message' => 'Article mis en brouillon.',
            'data'    => new ArticleResource($article),
        ]);
    }

    /**
     * POST /api/articles/{id}/like (auth)
     */
    public function like(Article $article): JsonResponse
    {
        $user = auth()->user();

        if ($article->likedBy()->where('user_id', $user->id)->exists()) {
            $article->likedBy()->detach($user->id);
            $article->decrement('likes_count');
            $message = 'Like retiré.';
        } else {
            $article->likedBy()->attach($user->id);
            $article->increment('likes_count');
            $message = 'Article aimé.';
        }

        return response()->json([
            'message'     => $message,
            'likes_count' => $article->fresh()->likes_count,
            'is_liked'    => $article->likedBy()->where('user_id', $user->id)->exists(),
        ]);
    }

    /**
     * GET /api/articles/featured
     */
    public function featured(): JsonResponse
    {
        $articles = Cache::remember('articles_featured', 600, function () {
            return Article::with(['author:id,name,avatar', 'category:id,name,slug,color'])
                         ->published()
                         ->featured()
                         ->orderBy('published_at', 'desc')
                         ->limit(5)
                         ->get();
        });

        return response()->json([
            'data' => ArticleResource::collection($articles),
        ]);
    }

    /**
     * GET /api/articles/{slug}/related
     */
    public function related(string $slug): JsonResponse
    {
        $article = Article::where('slug', $slug)->firstOrFail();

        $related = Article::with(['author:id,name', 'category:id,name,slug,color'])
                         ->published()
                         ->where('id', '!=', $article->id)
                         ->where(function ($q) use ($article) {
                             $q->where('category_id', $article->category_id)
                               ->orWhereJsonOverlaps('tags', $article->tags ?? []);
                         })
                         ->orderBy('published_at', 'desc')
                         ->limit(4)
                         ->get();

        return response()->json([
            'data' => ArticleResource::collection($related),
        ]);
    }
}
