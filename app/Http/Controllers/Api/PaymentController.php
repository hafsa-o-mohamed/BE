<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function create(Request $request)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('TAP_SECRET_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.tap.company/v2/charges', [
            'amount' => $request->amount,
            'currency' => 'KWD',
            'threeDSecure' => true,
            'save_card' => false,
            'description' => 'طلب جديد',
            'statement_descriptor' => 'Tap Payment',
            'metadata' => ['order_id' => '123456'],
            'customer' => [
                'first_name' => $request->name,
                'email' => $request->email,
            ],
            'source' => ['id' => 'src_all'],
            'redirect' => ['url' => 'com.tmahur.bundle://payment-result'],
        ]);

        return response()->json([
            'paymentUrl' => $response['transaction']['url'],
        ]);
    }
}