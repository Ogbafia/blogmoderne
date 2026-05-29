<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\NewsletterSubscriber;

class NewsletterController extends Controller
{
    /**
     * POST /api/newsletter/subscribe
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'name'  => 'nullable|string|max:100',
        ]);

        $subscriber = NewsletterSubscriber::firstOrCreate(
            ['email' => $validated['email']],
            ['name'  => $validated['name'] ?? null, 'is_active' => true]
        );

        if (! $subscriber->is_active) {
            $subscriber->update(['is_active' => true, 'unsubscribed_at' => null]);
        }

        return response()->json([
            'message' => 'Abonnement confirmé ! Merci de rejoindre notre newsletter.',
        ]);
    }

    /**
     * GET /api/newsletter/unsubscribe/{token}
     */
    public function unsubscribe(string $token): JsonResponse
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->firstOrFail();
        $subscriber->unsubscribe();

        return response()->json([
            'message' => 'Vous avez été désabonné avec succès.',
        ]);
    }

    /**
     * GET /api/admin/newsletter/subscribers (admin)
     */
    public function subscribers(Request $request): JsonResponse
    {
        $subscribers = NewsletterSubscriber::query()
            ->when($request->boolean('active'), fn ($q) => $q->where('is_active', true))
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json([
            'data'  => $subscribers,
            'total' => NewsletterSubscriber::where('is_active', true)->count(),
        ]);
    }
}
