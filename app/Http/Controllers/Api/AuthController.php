<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Login with email and password, issuing a simple API token.
     */
    public function login(Request $request): JsonResponse
    {
        try {
            Log::info('Auth.login: incoming request', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload_keys' => array_keys($request->all()),
                'url' => $request->fullUrl(),
            ]);

            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            $user = User::where('email', $validated['email'])->first();
            if (!$user || !Hash::check($validated['password'], $user->password)) {
                Log::warning('Auth.login: invalid credentials', [
                    'email' => $validated['email'],
                    'ip' => $request->ip(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Identifiants invalides.',
                ], 401);
            }

            // Generate and persist a new token
            $token = Str::random(60);
            $column = Schema::hasColumn('users', 'api_token') ? 'api_token' : 'remember_token';
            $user->$column = hash('sha256', $token); // store hashed
            $user->save();

            Log::info('Auth.login: success', [
                'user_id' => $user->id,
                'email' => $user->email,
                'column' => $column,
            ]);

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Auth.login: exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur.',
            ], 500);
        }
    }

    /**
     * Logout by revoking the current token.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $token = $this->extractBearerToken($request);
            Log::info('Auth.logout: incoming request', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'has_token' => (bool) $token,
            ]);

            if (!$token) {
                Log::warning('Auth.logout: missing token');
                return response()->json([
                    'success' => false,
                    'message' => 'Token manquant.',
                ], 400);
            }

            $hashed = hash('sha256', $token);
            $column = Schema::hasColumn('users', 'api_token') ? 'api_token' : 'remember_token';
            $user = User::where($column, $hashed)->first();
            if ($user) {
                $user->$column = null;
                $user->save();
                Log::info('Auth.logout: token revoked', [
                    'user_id' => $user->id,
                    'column' => $column,
                ]);
            } else {
                Log::warning('Auth.logout: no user for token');
            }

            return response()->json([
                'success' => true,
                'message' => 'Déconnexion réussie.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Auth.logout: exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur.',
            ], 500);
        }
    }

    /**
     * Return current user based on bearer token.
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $token = $this->extractBearerToken($request);
            Log::info('Auth.me: incoming request', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'has_token' => (bool) $token,
            ]);

            if (!$token) {
                Log::warning('Auth.me: missing token');
                return response()->json([
                    'success' => false,
                    'message' => 'Token manquant.',
                ], 400);
            }

            $hashed = hash('sha256', $token);
            $column = Schema::hasColumn('users', 'api_token') ? 'api_token' : 'remember_token';
            $user = User::where($column, $hashed)->first();
            if (!$user) {
                Log::warning('Auth.me: invalid token');
                return response()->json([
                    'success' => false,
                    'message' => 'Token invalide.',
                ], 401);
            }

            Log::info('Auth.me: success', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Auth.me: exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur.',
            ], 500);
        }
    }

    private function extractBearerToken(Request $request): ?string
    {
        $header = $request->header('Authorization');
        if (!$header) return null;
        if (str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }
        return null;
    }

    /**
     * Dev-only: create a default admin user with known credentials.
     */
    public function bootstrapAdmin(Request $request): JsonResponse
    {
        // Only allow in local/dev environments
        if (!app()->environment(['local', 'development']) && !config('app.debug')) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé en production.',
            ], 403);
        }

        $email = 'admin@example.com';
        $password = 'secret123';

        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Admin',
                'email' => $email,
                'password' => Hash::make($password),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Compte admin prêt pour les tests.',
            'credentials' => [
                'email' => $email,
                'password' => $password,
            ],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }
}