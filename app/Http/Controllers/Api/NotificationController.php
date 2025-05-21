<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function updateToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $user = auth()->user();
        $user->fcm_token = $request->token;  // We're still using fcm_token column but storing Expo token
        $user->save();

        return response()->json([
            'message' => 'Notification token updated successfully',
            'token' => $user->fcm_token
        ]);
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string'
        ]);

        $user = auth()->user();
        if (!$user->fcm_token) {
            return response()->json([
                'message' => 'No notification token found for this user'
            ], 404);
        }

        try {
            $response = $this->sendExpoNotification(
                $user->fcm_token,
                $request->title,
                $request->body
            );
            
            return response()->json([
                'message' => 'Notification sent successfully',
                'response' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function sendExpoNotification($token, $title, $body)
    {
        $message = [
            'to' => $token,
            'sound' => 'default',
            'title' => $title,
            'body' => $body,
        ];

        $ch = curl_init('https://exp.host/--/api/v2/push/send');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Accept-encoding: gzip, deflate',
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
} 