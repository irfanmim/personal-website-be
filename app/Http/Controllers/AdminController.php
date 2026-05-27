<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAdminPasswordRequest;
use App\Http\Requests\UpdateAdminUsernameRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Update the admin username.
     *
     * PUT /api/admin/username
     * Body: { "username": "newname" }
     */
    public function updateUsername(UpdateAdminUsernameRequest $request): JsonResponse
    {
        $request->user()->update([
            'username' => $request->username,
        ]);

        return response()->json([
            'username' => $request->user()->fresh()->username,
        ]);
    }

    /**
     * Update the admin password.
     *
     * PUT /api/admin/password
     * Body: { "current_password": "...", "password": "...", "password_confirmation": "..." }
     */
    public function updatePassword(UpdateAdminPasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'error'   => 'Validation failed',
                'details' => ['current_password' => ['The current password is incorrect.']],
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Revoke all tokens — the admin must log in again with the new password.
        $user->tokens()->delete();

        return response()->json(['message' => 'Password updated. Please log in again.']);
    }
}
