<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TapPayment\ApplePayService;
use App\Services\TapPayment\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TapPaymentController extends Controller
{
    /**
     * The Apple Pay service instance.
     *
     * @var \App\Services\TapPayment\ApplePayService
     */
    protected $applePayService;

    /**
     * The webhook service instance.
     *
     * @var \App\Services\TapPayment\WebhookService
     */
    protected $webhookService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\TapPayment\ApplePayService $applePayService
     * @param \App\Services\TapPayment\WebhookService $webhookService
     * @return void
     */
    public function __construct(ApplePayService $applePayService, WebhookService $webhookService)
    {
        $this->applePayService = $applePayService;
        $this->webhookService = $webhookService;
    }

    /**
     * Process Apple Pay payment
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createCharge(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.1',
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'description' => 'nullable|string',
            'reference_id' => 'nullable|string',
            'token_data' => 'required|array',
            'token_data.data' => 'required|string',
            'token_data.header' => 'required|array',
            'token_data.header.ephemeralPublicKey' => 'required|string',
            'token_data.header.publicKeyHash' => 'required|string',
            'token_data.header.transactionId' => 'required|string',
            'token_data.signature' => 'required|string',
            'token_data.version' => 'required|string',
        ]);

        try {
            // Prepare payment data
            $paymentData = [
                'amount' => $validated['amount'],
                'currency' => 'KWD',
                'description' => $validated['description'] ?? 'Payment',
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $request->input('customer_phone'),
                'reference_id' => $validated['reference_id'] ?? null,
                'token_data' => $validated['token_data'],
                'ip_address' => $request->ip(),
                'user_id' => auth()->id(),
            ];

            // Process the Apple Pay payment
            $result = $this->applePayService->processApplePayPayment($paymentData);

            // Return successful response
            return response()->json([
                'success' => true,
                'transaction_id' => $result['transaction_id'],
                'charge_id' => $result['charge_id'],
                'status' => $result['status'],
                'payment_url' => $result['payment_url']
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Apple Pay payment processing failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the payment',
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
        try {
            // Process the webhook using the webhook service
            $result = $this->webhookService->processWebhook($request);
            
            // Always return 200 OK to Tap to acknowledge receipt
            return response()->json([
                'status' => $result['status'] ?? 'received',
                'message' => $result['message'] ?? 'Webhook processed'
            ]);
        } catch (\Exception $e) {
            // Log the error but still return 200 OK to Tap
            Log::error('Error processing Tap Payment webhook', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Always acknowledge receipt to prevent retries
            return response()->json(['status' => 'received']);
        }
    }
}
