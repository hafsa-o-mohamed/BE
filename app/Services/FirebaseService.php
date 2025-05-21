<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $serverKey;
    protected $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    public function __construct()
    {
        $this->serverKey = env('FIREBASE_SERVER_KEY');
    }

    public function sendToUser($token, $title, $body, $data = [])
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($this->fcmUrl, [
                'to' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                ],
                'data' => $data,
            ]);

            Log::info('FCM Response', ['response' => $response->json()]);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('FCM Error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function sendToMultiple($tokens, $title, $body, $data = [])
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($this->fcmUrl, [
                'registration_ids' => $tokens,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                ],
                'data' => $data,
            ]);

            Log::info('FCM Multicast Response', ['response' => $response->json()]);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('FCM Multicast Error', ['error' => $e->getMessage()]);
            return false;
        }
    }
}