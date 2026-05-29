<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Article;

class CommentController extends Controller
{
    /**
     * GET /api/articles/{articleId}/comments
     */
    public function index(string $articleId): JsonResponse
    {
        $comments = Comment::with(['author:id,name,avatar', 'replies.author:id,name,avatar'])
            ->where('article_id', $articleId)
            ->whereNull('parent_id')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'data' => $comments,
        ]);
    }

    /**
     * POST /api/articles/{articleId}/comments (auth)
     */
    public function store(Request $request, string $articleId): JsonResponse
    {
        $validated = $request->validate([
            'content'   => 'required|string|min:3|max:2000',
            'parent_id' => 'nullable|uuid|exists:comments,id',
        ]);

        $article = Article::published()->findOrFail($articleId);

        $comment = $article->allComments()->create([
            'content'   => $validated['content'],
            'user_id'   => auth()->id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'status'    => 'approved',
        ]);

        $comment->load('author:id,name,avatar');

        return response()->json([
            'message' => 'Commentaire ajouté.',
            'data'    => $comment,
        ], 201);
    }

    /**
     * PUT /api/comments/{id} (auth + ownership)
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $comment = Comment::findOrFail($id);
        // $this->authorize('update', $comment);

        $validated = $request->validate(['content' => 'required|string|min:3|max:2000']);

        $comment->update($validated);

        return response()->json([
            'message' => 'Commentaire modifié.',
            'data'    => $comment,
        ]);
    }

    /**
     * DELETE /api/comments/{id} (auth + ownership ou admin)
     */
    public function destroy(string $id): JsonResponse
    {
        $comment = Comment::findOrFail($id);
        // $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json(['message' => 'Commentaire supprimé.']);
    }
}
