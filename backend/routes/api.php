<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AdminController;

// ════════════════════════════════════════════════════════════════════════════
//  ROUTES PUBLIQUES
// ════════════════════════════════════════════════════════════════════════════

// ─── Auth ────────────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// ─── Articles publics ────────────────────────────────────────────────────────
Route::prefix('articles')->group(function () {
    Route::get('/',              [ArticleController::class, 'index']);      // Liste + filtres
    Route::get('/featured',      [ArticleController::class, 'featured']);   // Articles mis en avant
    Route::get('/{slug}',        [ArticleController::class, 'show']);       // Détail par slug
    Route::get('/{slug}/related',[ArticleController::class, 'related']);   // Articles similaires
    Route::get('/{id}/comments', [CommentController::class, 'index']);     // Commentaires
});

// ─── Catégories publiques ─────────────────────────────────────────────────────
Route::prefix('categories')->group(function () {
    Route::get('/',       [CategoryController::class, 'index']);   // Liste toutes les catégories
    Route::get('/{slug}', [CategoryController::class, 'show']);    // Détail + articles
});

// ─── Newsletter ───────────────────────────────────────────────────────────────
Route::prefix('newsletter')->group(function () {
    Route::post('/subscribe',           [NewsletterController::class, 'subscribe']);
    Route::get('/unsubscribe/{token}',  [NewsletterController::class, 'unsubscribe']);
});

// ─── Recherche globale ────────────────────────────────────────────────────────
Route::get('/search', [ArticleController::class, 'search']);

// ─── Tags ────────────────────────────────────────────────────────────────────
Route::get('/tags', fn () => response()->json([
    'data' => \App\Models\Article::published()
                ->get()
                ->flatMap(fn ($a) => $a->tags ?? [])
                ->countBy()
                ->sortDesc()
                ->map(fn ($count, $tag) => ['tag' => $tag, 'count' => $count])
                ->values(),
]));

// ════════════════════════════════════════════════════════════════════════════
//  ROUTES AUTHENTIFIÉES
// ════════════════════════════════════════════════════════════════════════════

Route::middleware('auth:sanctum')->group(function () {

    // ─── Auth ──────────────────────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('/logout',       [AuthController::class, 'logout']);
        Route::get('/me',            [AuthController::class, 'me']);
        Route::put('/profile',       [AuthController::class, 'updateProfile']);
        Route::post('/avatar',       [AuthController::class, 'updateAvatar']);
    });

    // ─── Articles (création/modification) ─────────────────────────────────
    Route::prefix('articles')->group(function () {
        Route::post('/',                [ArticleController::class, 'store']);
        Route::put('/{article}',        [ArticleController::class, 'update']);
        Route::delete('/{article}',     [ArticleController::class, 'destroy']);
        Route::post('/{article}/like',  [ArticleController::class, 'like']);
    });

    // ─── Commentaires ──────────────────────────────────────────────────────
    Route::prefix('articles/{articleId}')->group(function () {
        Route::post('/comments',        [CommentController::class, 'store']);
    });
    Route::prefix('comments')->group(function () {
        Route::put('/{comment}',        [CommentController::class, 'update']);
        Route::delete('/{comment}',     [CommentController::class, 'destroy']);
    });

    // ─── Médias ────────────────────────────────────────────────────────────
    Route::prefix('media')->group(function () {
        Route::post('/upload',          [MediaController::class, 'upload']);
        Route::delete('/{media}',       [MediaController::class, 'destroy']);
    });

    // ════════════════════════════════════════════════════════════════════════
    //  ROUTES ADMIN UNIQUEMENT
    // ════════════════════════════════════════════════════════════════════════

    Route::middleware('admin')->prefix('admin')->group(function () {

        // Dashboard stats
        Route::get('/stats',            [AdminController::class, 'stats']);

        // Articles admin (brouillons inclus)
        Route::get('/articles',         [AdminController::class, 'articles']);
        Route::post('/articles/{article}/publish',   [ArticleController::class, 'publish']);
        Route::post('/articles/{article}/unpublish', [ArticleController::class, 'unpublish']);

        // Commentaires modération
        Route::get('/comments',                         [AdminController::class, 'comments']);
        Route::post('/comments/{comment}/approve',      [CommentController::class, 'approve']);
        Route::post('/comments/{comment}/reject',       [CommentController::class, 'reject']);

        // Médias
        Route::get('/media',            [MediaController::class, 'index']);

        // Newsletter
        Route::get('/newsletter/subscribers', [NewsletterController::class, 'subscribers']);

        // Catégories (CRUD complet)
        Route::apiResource('categories', CategoryController::class);

        // Utilisateurs
        Route::get('/users',            [AdminController::class, 'users']);
        Route::put('/users/{user}/role',[AdminController::class, 'updateUserRole']);
        Route::delete('/users/{user}',  [AdminController::class, 'destroyUser']);
    });
});
