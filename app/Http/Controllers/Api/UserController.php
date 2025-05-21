<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Apartment;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $user->load(['owner.apartments' => function($query) {
            $query->with(['building.project'])->first();
        }]);
        
        $apartment = $user->owner?->apartments()->with(['building.project'])->first();
        
        $response = [
            'user' => $user,
            'apartment' => $apartment ?? null, // Use null if no apartment found
            'owner' => $user->owner ?? null // Use null if no owner found
        ];

        return response()->json($response);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $request->user()->id,
        ]);

        $request->user()->update($validated);

        return response()->json([
            'message' => 'Profile updated',
            'user' => $request->user()
        ]);
    }

    public function updatePhone(Request $request)
    {   
        $validated = $request->validate([
            'phone_number' => 'required|max:20',
        ]);

        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            $user->update([
                'phone_number' => $validated['phone_number']
            ]);

            return response()->json([
                'message' => 'Phone number updated successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update phone number',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            // Delete the user's account
            $user->delete();

            return response()->json([
                'message' => 'Account deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete account',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 


