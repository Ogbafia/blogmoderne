<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * POST /api/auth/register
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role'      => 'reader',
            'is_active' => true,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inscription réussie.',
            'token'   => $token,
            'user'    => $user->only(['id', 'name', 'email', 'role', 'avatar']),
        ], 201);
    }

    /**
     * POST /api/auth/login
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Identifiants incorrects.',
            ], 401);
        }

        if ($user->is_active === false) {
            return response()->json([
                'message' => 'Votre compte a été désactivé.',
            ], 403);
        }

        // Révoquer les anciens tokens si besoin
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie.',
            'token'   => $token,
            'user'    => $user->only(['id', 'name', 'email', 'role', 'avatar']),
        ]);
    }

    /**
     * POST /api/auth/logout (auth)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Déconnexion réussie.']);
    }

    /**
     * GET /api/auth/me (auth)
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()]);
    }

    /**
     * PUT /api/auth/profile (auth)
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'sometimes|required|string|max:100',
            'bio'      => 'nullable|string|max:500',
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $user = $request->user();

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profil mis à jour.',
            'data'    => $user->fresh()->only(['id', 'name', 'email', 'role', 'avatar', 'bio', 'is_active']),
        ]);
    }

    /**
     * POST /api/auth/avatar (auth)
     */
    public function updateAvatar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = $request->user();

        if ($user->avatar) {
            $oldPath = str_replace('/storage/', '', parse_url($user->avatar, PHP_URL_PATH) ?: $user->avatar);
            Storage::disk('public')->delete($oldPath);
        }

        $path = $validated['avatar']->store('avatars', 'public');
        $user->update([
            'avatar' => Storage::disk('public')->url($path),
        ]);

        return response()->json([
            'message' => 'Avatar mis à jour.',
            'data'    => $user->fresh()->only(['id', 'name', 'email', 'role', 'avatar', 'bio', 'is_active']),
        ]);
    }
}
