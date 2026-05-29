<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function stats(): JsonResponse
    {
        return response()->json([
            'stats' => [
                'articles' => Article::count(),
                'comments' => Comment::count(),
                'users' => User::count(),
            ]
        ]);
    }

    public function articles(): JsonResponse
    {
        return response()->json([
            'data' => Article::with('author', 'category')->orderBy('created_at', 'desc')->paginate(20),
        ]);
    }

    public function comments(): JsonResponse
    {
        return response()->json([
            'data' => Comment::with('author', 'article')->orderBy('created_at', 'desc')->paginate(20),
        ]);
    }

    public function users(): JsonResponse
    {
        return response()->json([
            'data' => User::orderBy('name')->paginate(20),
        ]);
    }

    public function updateUserRole(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate(['role' => 'required|in:admin,author,reader']);
        $user->update($validated);
        return response()->json($user);
    }

    public function destroyUser(User $user): JsonResponse
    {
        $user->delete();
        return response()->json(null, 204);
    }
}
