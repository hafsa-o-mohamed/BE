<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TapPayment\ApplePayService;
use App\Services\TapPayment\PaymentAgreementService;
use App\Services\TapPayment\WebhookService;
use App\Models\TapPaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Controller for handling Tap Payments API endpoints
 * 
 * @source https://developers.tap.company/docs
 */
class TapPaymentController extends Controller
{
    /**
     * Apple Pay service
     * 
     * @var ApplePayService
     */
    protected $applePayService;
    
    /**
     * Payment Agreement service
     * 
     * @var PaymentAgreementService
     */
    protected $paymentAgreementService;
    
    /**
     * Webhook service
     * 
     * @var WebhookService
     */
    protected $webhookService;

    /**
     * Constructor
     * 
     * @param ApplePayService $applePayService
     * @param PaymentAgreementService $paymentAgreementService
     * @param WebhookService $webhookService
     */
    public function __construct(
        ApplePayService $applePayService,
        PaymentAgreementService $paymentAgreementService,
        WebhookService $webhookService
    ) {
        $this->applePayService = $applePayService;
        $this->paymentAgreementService = $paymentAgreementService;
        $this->webhookService = $webhookService;
    }

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
            'token_id' => 'nullable|string',
        ]);

        try {
            // Prepare charge data
            $chargeData = [
                'amount' => $validated['amount'],
                'currency' => 'KWD',
                'customer' => [
                    'first_name' => $validated['name'],
                    'email' => $validated['email'],
                ],
                'description' => $validated['description'] ?? 'طلب جديد',
                'statement_descriptor' => 'Tap Payment',
                'reference' => [
                    'order' => $validated['reference_id'] ?? null
                ],
                'metadata' => $validated['metadata'] ?? [],
                'redirect' => ['url' => env('TAP_REDIRECT_URL', 'com.tmahur.bundle://payment-result')],
            ];

            // If token_id is provided, use it to create a charge
            if (isset($validated['token_id'])) {
                $result = $this->applePayService->createCharge(
                    $validated['token_id'],
                    $chargeData,
                    $request->header('Idempotency-Key')
                );
            } else {
                // Use default source for regular card payments
                $chargeData['source'] = ['id' => 'src_all'];
                
                // Create a new transaction record
                $transaction = TapPaymentTransaction::create([
                    'transaction_id' => 'txn_' . Str::uuid(),
                    'amount' => $validated['amount'],
                    'currency' => 'KWD',
                    'status' => 'pending',
                    'user_id' => auth()->id(),
                    'request_data' => $chargeData,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
                
                // Use the ApplePayService to create a charge with the default source
                $result = $this->applePayService->createCharge(
                    'src_all',
                    $chargeData,
                    $request->header('Idempotency-Key')
                );
            }

            // Return the result
            if ($result['success']) {
                return response()->json($result);
            } else {
                return response()->json($result, 422);
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
            // Find the charge in our database first
            $transaction = TapPaymentTransaction::where('charge_id', $chargeId)->first();
            
            if ($transaction) {
                return response()->json([
                    'success' => true,
                    'transaction_id' => $transaction->transaction_id,
                    'charge_id' => $transaction->charge_id,
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                    'status' => $transaction->status,
                    'created_at' => $transaction->created_at,
                    'updated_at' => $transaction->updated_at,
                    'response_data' => $transaction->response_data,
                ]);
            }
            
            // If not found in our database, fetch from Tap API
            $response = $this->applePayService->makeRequest('get', 'charges/' . $chargeId);
            
            if ($response->successful()) {
                $responseData = $response->json();
                
                // Create a transaction record if it doesn't exist
                if (!$transaction) {
                    TapPaymentTransaction::create([
                        'transaction_id' => 'txn_' . Str::uuid(),
                        'charge_id' => $chargeId,
                        'amount' => $responseData['amount'] ?? 0,
                        'currency' => $responseData['currency'] ?? 'KWD',
                        'status' => strtolower($responseData['status'] ?? 'unknown'),
                        'response_data' => $responseData,
                    ]);
                }
                
                return response()->json([
                    'success' => true,
                    'charge' => $responseData
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
        try {
            // Use the webhook service to process the webhook
            $result = $this->webhookService->processWebhook($request);
            
            // Always return 200 OK to Tap to acknowledge receipt
            return response()->json(['status' => 'received']);
        } catch (\Exception $e) {
            // Log the error but still return 200 OK to Tap
            Log::error('Error processing Tap Payment webhook', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Always acknowledge receipt to prevent retries
            return response()->json(['status' => 'received']);
        }
    }

    /**
     * Process an Apple Pay token
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @source https://developers.tap.company/docs/apple-pay-token
     */
    public function processApplePayToken(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'token_data' => 'required|array',
            'token_data.data' => 'required|string',
            'token_data.header' => 'required|array',
            'token_data.header.ephemeralPublicKey' => 'required|string',
            'token_data.header.publicKeyHash' => 'required|string',
            'token_data.header.transactionId' => 'required|string',
            'token_data.signature' => 'required|string',
            'token_data.version' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Apple Pay token data',
                'errors' => $validator->errors()->toArray(),
            ], 422);
        }

        try {
            // Process the Apple Pay token
            $result = $this->applePayService->processApplePayToken(
                $request->input('token_data'),
                $request->ip(),
                $request->header('Idempotency-Key')
            );
            
            // Return the result
            if ($result['success']) {
                return response()->json($result);
            } else {
                return response()->json($result, 422);
            }
        } catch (\Exception $e) {
            Log::error('Exception processing Apple Pay token', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the Apple Pay token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Create a payment agreement
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @source https://developers.tap.company/docs/creating-payment-agreement
     */
    public function createPaymentAgreement(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'token_id' => 'required|string',
            'contract_type' => 'required|string|in:UNSCHEDULED,SUBSCRIPTION,INSTALLMENT,MILESTONE,ORDER',
            'customer' => 'required|array',
            'customer.first_name' => 'required|string',
            'customer.email' => 'required|email',
            'currency' => 'required|string|size:3',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment agreement data',
                'errors' => $validator->errors()->toArray(),
            ], 422);
        }

        try {
            // Create the payment agreement
            $result = $this->paymentAgreementService->createAgreement(
                $request->input('token_id'),
                $request->all(),
                $request->header('Idempotency-Key')
            );
            
            // Return the result
            if ($result['success']) {
                return response()->json($result);
            } else {
                return response()->json($result, 422);
            }
        } catch (\Exception $e) {
            Log::error('Exception creating payment agreement', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the payment agreement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Create a merchant-initiated charge
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @source https://developers.tap.company/docs/merchant-initiated-transaction
     */
    public function createMerchantInitiatedCharge(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'agreement_id' => 'required|string',
            'amount' => 'required|numeric|min:0.1',
            'currency' => 'required|string|size:3',
            'customer_id' => 'required|string',
            'token_id' => 'required|string',
            'description' => 'nullable|string',
            'reference' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid charge data',
                'errors' => $validator->errors()->toArray(),
            ], 422);
        }

        try {
            // Create the merchant-initiated charge
            $result = $this->paymentAgreementService->createMerchantInitiatedCharge(
                $request->input('agreement_id'),
                $request->all(),
                $request->header('Idempotency-Key')
            );
            
            // Return the result
            if ($result['success']) {
                return response()->json($result);
            } else {
                return response()->json($result, 422);
            }
        } catch (\Exception $e) {
            Log::error('Exception creating merchant-initiated charge', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the merchant-initiated charge',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Get transaction history
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransactionHistory(Request $request)
    {
        try {
            // Get the user's transaction history
            $transactions = TapPaymentTransaction::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->paginate($request->input('per_page', 15));
            
            return response()->json([
                'success' => true,
                'transactions' => $transactions,
            ]);
        } catch (\Exception $e) {
            Log::error('Exception getting transaction history', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the transaction history',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
