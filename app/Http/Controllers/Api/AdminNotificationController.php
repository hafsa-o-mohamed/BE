<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use App\Models\User;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function sendToAll(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'data' => 'nullable|array'
        ]);

        $users = User::whereNotNull('device_token')->get();
        
        if ($users->isEmpty()) {
            return response()->json([
                'message' => 'No users with registered devices found'
            ], 404);
        }

        $tokens = $users->pluck('device_token')->toArray();
        
        $success = $this->firebaseService->sendToMultiple(
            $tokens,
            $request->title,
            $request->body,
            $request->data ?? []
        );

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Notification sent successfully' : 'Failed to send notification'
        ]);
    }

    public function sendToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'body' => 'required|string',
            'data' => 'nullable|array'
        ]);

        $user = User::find($request->user_id);
        
        if (!$user->device_token) {
            return response()->json([
                'message' => 'User has no registered device'
            ], 404);
        }

        $success = $this->firebaseService->sendToUser(
            $user->device_token,
            $request->title,
            $request->body,
            $request->data ?? []
        );

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Notification sent successfully' : 'Failed to send notification'
        ]);
    }
}