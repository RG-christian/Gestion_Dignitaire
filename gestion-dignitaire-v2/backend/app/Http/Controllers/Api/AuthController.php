<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Connexion
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Charger les fonctions et sous-fonctions
            $user->load(['fonctions', 'sousfonctions']);

            // Supprimer les anciens tokens
            $user->tokens()->delete();

            // Créer un nouveau token avec expiration de 7 jours
            $token = $user->createToken('auth-token', ['*'], now()->addDays(7))->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
                'expires_at' => now()->addDays(7)->toIso8601String(),
                'message' => 'Connexion réussie'
            ]);
        }

        return response()->json([
            'message' => 'Identifiants invalides'
        ], 401);
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ]);
    }

    /**
     * Utilisateur connecté
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load(['fonctions', 'sousfonctions']);
        return response()->json($user);
    }

    /**
     * Inscription (optionnel)
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users',
            'nom_complet' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
            'message' => 'Inscription réussie'
        ], 201);
    }
}
