<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TapPaymentController extends Controller
{
    private $apiUrl = 'https://api.tap.company/v2';
    private $backupDir = 'tap_payments_backup';

    /**
     * Create a new charge
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createCharge(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.1',
            'name' => 'required|string',
            'email' => 'required|email',
            'description' => 'nullable|string',
            'reference_id' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        try {
            // Generate a unique transaction ID
            $transactionId = 'txn_' . Str::uuid();
            
            // Prepare charge data
            $chargeData = [
                'amount' => $validated['amount'],
                'currency' => 'KWD',
                'threeDSecure' => true,
                'save_card' => false,
                'description' => $validated['description'] ?? 'طلب جديد',
                'statement_descriptor' => 'Tap Payment',
                'reference' => [
                    'transaction' => $transactionId,
                    'order' => $validated['reference_id'] ?? $transactionId
                ],
                'metadata' => $validated['metadata'] ?? ['transaction_id' => $transactionId],
                'customer' => [
                    'first_name' => $validated['name'],
                    'email' => $validated['email'],
                ],
                'source' => ['id' => 'src_all'],
                'redirect' => ['url' => env('TAP_REDIRECT_URL', 'com.tmahur.bundle://payment-result')],
            ];

            // Create charge via Tap API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('TAP_SECRET_KEY'),
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/charges', $chargeData);

            // Backup charge data to file
            $this->backupToFile('charge_created', [
                'transaction_id' => $transactionId,
                'request' => $chargeData,
                'response' => $response->json(),
                'timestamp' => now()->toIso8601String()
            ]);

            // Return response
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'payment_url' => $response['transaction']['url'],
                    'charge_id' => $response['id']
                ]);
            } else {
                Log::error('Tap Payment charge creation failed', [
                    'error' => $response->json(),
                    'request' => $chargeData
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create payment',
                    'error' => $response->json()
                ], 422);
            }
        } catch (\Exception $e) {
            Log::error('Tap Payment exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get charge details
     * 
     * @param string $chargeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCharge($chargeId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('TAP_SECRET_KEY'),
                'Content-Type' => 'application/json',
            ])->get($this->apiUrl . '/charges/' . $chargeId);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'charge' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to retrieve charge details',
                    'error' => $response->json()
                ], 422);
            }
        } catch (\Exception $e) {
            Log::error('Tap Payment get charge exception', [
                'message' => $e->getMessage(),
                'charge_id' => $chargeId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving charge details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle webhook from Tap Payments
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function webhook(Request $request)
    {
        // Verify webhook signature if available
        if ($request->header('Tap-Signature')) {
            // TODO: Implement signature verification when needed
        }

        // Get the webhook payload
        $payload = $request->all();
        
        // Backup webhook data to file
        $this->backupToFile('webhook_received', [
            'payload' => $payload,
            'headers' => $request->headers->all(),
            'timestamp' => now()->toIso8601String()
        ]);

        try {
            // Process webhook based on event type
            $eventType = $payload['event'] ?? 'unknown';
            
            switch ($eventType) {
                case 'charge.succeeded':
                    // Handle successful charge
                    $this->handleSuccessfulCharge($payload);
                    break;
                    
                case 'charge.failed':
                    // Handle failed charge
                    $this->handleFailedCharge($payload);
                    break;
                    
                default:
                    // Log unknown event type
                    Log::info('Tap Payment webhook received with unknown event type', [
                        'event_type' => $eventType,
                        'payload' => $payload
                    ]);
            }
            
            // Always return 200 OK to Tap to acknowledge receipt
            return response()->json(['status' => 'received']);
            
        } catch (\Exception $e) {
            // Log the error but still return 200 OK to Tap
            Log::error('Error processing Tap Payment webhook', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $payload
            ]);
            
            // Always acknowledge receipt to prevent retries
            return response()->json(['status' => 'received']);
        }
    }

    /**
     * Handle successful charge
     * 
     * @param array $payload
     * @return void
     */
    private function handleSuccessfulCharge($payload)
    {
        // Extract charge data
        $chargeId = $payload['data']['id'] ?? null;
        $amount = $payload['data']['amount'] ?? 0;
        $currency = $payload['data']['currency'] ?? 'KWD';
        $customerId = $payload['data']['customer']['id'] ?? null;
        $transactionId = $payload['data']['reference']['transaction'] ?? null;
        $orderId = $payload['data']['reference']['order'] ?? null;
        
        // Log successful payment
        Log::info('Tap Payment successful', [
            'charge_id' => $chargeId,
            'amount' => $amount,
            'currency' => $currency,
            'transaction_id' => $transactionId,
            'order_id' => $orderId
        ]);
        
        // Backup successful charge data
        $this->backupToFile('charge_succeeded', [
            'charge_id' => $chargeId,
            'amount' => $amount,
            'currency' => $currency,
            'customer_id' => $customerId,
            'transaction_id' => $transactionId,
            'order_id' => $orderId,
            'payload' => $payload,
            'timestamp' => now()->toIso8601String()
        ]);
        
        // TODO: Update database with payment status
        // TODO: Trigger any necessary business logic (e.g., update order status, send confirmation email)
    }

    /**
     * Handle failed charge
     * 
     * @param array $payload
     * @return void
     */
    private function handleFailedCharge($payload)
    {
        // Extract charge data
        $chargeId = $payload['data']['id'] ?? null;
        $amount = $payload['data']['amount'] ?? 0;
        $currency = $payload['data']['currency'] ?? 'KWD';
        $customerId = $payload['data']['customer']['id'] ?? null;
        $transactionId = $payload['data']['reference']['transaction'] ?? null;
        $orderId = $payload['data']['reference']['order'] ?? null;
        $failureReason = $payload['data']['response']['message'] ?? 'Unknown reason';
        
        // Log failed payment
        Log::error('Tap Payment failed', [
            'charge_id' => $chargeId,
            'amount' => $amount,
            'currency' => $currency,
            'transaction_id' => $transactionId,
            'order_id' => $orderId,
            'failure_reason' => $failureReason
        ]);
        
        // Backup failed charge data
        $this->backupToFile('charge_failed', [
            'charge_id' => $chargeId,
            'amount' => $amount,
            'currency' => $currency,
            'customer_id' => $customerId,
            'transaction_id' => $transactionId,
            'order_id' => $orderId,
            'failure_reason' => $failureReason,
            'payload' => $payload,
            'timestamp' => now()->toIso8601String()
        ]);
        
        // TODO: Update database with payment status
        // TODO: Trigger any necessary business logic (e.g., notify customer, retry payment)
    }

    /**
     * Backup data to file as a safeguard
     * 
     * @param string $type
     * @param array $data
     * @return bool
     */
    private function backupToFile($type, $data)
    {
        try {
            // Create a unique filename
            $filename = $type . '_' . now()->format('Y-m-d_H-i-s') . '_' . Str::random(8) . '.txt';
            
            // Ensure directory exists
            if (!Storage::exists($this->backupDir)) {
                Storage::makeDirectory($this->backupDir);
            }
            
            // Write data to file
            Storage::put(
                $this->backupDir . '/' . $filename, 
                json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to backup Tap Payment data to file', [
                'message' => $e->getMessage(),
                'type' => $type
            ]);
            
            return false;
        }
    }
}
