<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Authenticate admin and return a Sanctum bearer token.
     * Credentials are validated against the admin user record in the database.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', 'admin@app.local')->firstOrFail();

        if (
            $request->username !== $user->username ||
            ! Hash::check($request->password, $user->password)
        ) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Revoke all existing tokens so each login issues a fresh one.
        $user->tokens()->delete();

        $expirationMinutes = config('sanctum.expiration'); // null = never
        $token = $user->createToken('admin-token');

        return response()->json([
            'token'     => $token->plainTextToken,
            'expiresIn' => $expirationMinutes ? $expirationMinutes * 60 : 604800,
        ]);
    }

    /**
     * Revoke the current access token.
     */
    public function logout(Request $request): \Illuminate\Http\Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }

    /**
     * Return the currently authenticated admin.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'username' => $request->user()->username,
        ]);
    }
}
