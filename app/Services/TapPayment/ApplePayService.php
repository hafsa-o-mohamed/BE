<?php

namespace App\Services\TapPayment;

use App\Models\TapPaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Service for handling Apple Pay transactions with Tap Payments
 * 
 * @source https://developers.tap.company/docs/apple-pay-token
 */
class ApplePayService extends TapPaymentService
{
    /**
     * Process an Apple Pay token to create a Tap token
     * 
     * @param array $tokenData Apple Pay token data
     * @param string $clientIp Client IP address
     * @param string|null $idempotencyKey Idempotency key for replay protection
     * @return array Response data
     * 
     * @source https://developers.tap.company/docs/apple-pay-token
     */
    public function processApplePayToken($tokenData, $clientIp, $idempotencyKey = null)
    {
        // Validate the Apple Pay token data
        $validator = Validator::make($tokenData, [
            'data' => 'required|string',
            'header' => 'required|array',
            'header.ephemeralPublicKey' => 'required|string',
            'header.publicKeyHash' => 'required|string',
            'header.transactionId' => 'required|string',
            'signature' => 'required|string',
            'version' => 'required|string',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Invalid Apple Pay token data',
                'errors' => $validator->errors()->toArray(),
            ];
        }

        // Check for replay protection if idempotency key is provided
        if ($idempotencyKey) {
            $existingTransaction = $this->findTransactionByIdempotencyKey($idempotencyKey);
            
            if ($existingTransaction) {
                Log::warning('Duplicate transaction attempt detected', [
                    'idempotency_key' => $idempotencyKey,
                    'existing_transaction' => $existingTransaction->transaction_id,
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Duplicate transaction detected',
                    'transaction_id' => $existingTransaction->transaction_id,
                ];
            }
        } else {
            // Generate an idempotency key if not provided
            $idempotencyKey = $this->generateIdempotencyKey();
        }

        // Generate a unique transaction ID
        $transactionId = $this->generateTransactionId();
        
        // Prepare the request data for Tap API
        $requestData = [
            'type' => 'applepay',
            'token_data' => $tokenData,
            'client_ip' => $clientIp,
        ];
        
        // Backup the request data
        $this->backupToFile('apple_pay_token_request', [
            'transaction_id' => $transactionId,
            'request' => $requestData,
            'timestamp' => now()->toIso8601String(),
        ]);
        
        try {
            // Create a transaction record in pending state
            $transaction = $this->createTransactionRecord([
                'transaction_id' => $transactionId,
                'status' => 'pending',
                'payment_method' => 'APPLEPAY',
                'request_data' => $requestData,
                'idempotency_key' => $idempotencyKey,
                'ip_address' => $clientIp,
                'is_live' => $this->liveMode,
            ]);
            
            // Make the API request to Tap
            $response = $this->makeRequest('post', 'tokens', $requestData);
            
            // Process the response
            if ($response->successful()) {
                $responseData = $response->json();
                
                // Update the transaction record with the token details
                $this->updateTransactionRecord($transactionId, [
                    'token_id' => $responseData['id'] ?? null,
                    'status' => 'token_created',
                    'payment_type' => $responseData['card']['funding'] ?? null,
                    'card_brand' => $responseData['card']['brand'] ?? null,
                    'card_last_four' => $responseData['card']['last_four'] ?? null,
                    'card_first_six' => $responseData['card']['first_six'] ?? null,
                    'response_data' => $responseData,
                ]);
                
                // Backup the response data
                $this->backupToFile('apple_pay_token_response', [
                    'transaction_id' => $transactionId,
                    'response' => $responseData,
                    'timestamp' => now()->toIso8601String(),
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Apple Pay token processed successfully',
                    'transaction_id' => $transactionId,
                    'token_id' => $responseData['id'],
                    'card' => $responseData['card'] ?? null,
                ];
            } else {
                $errorData = $response->json();
                
                // Update the transaction record with the error details
                $this->updateTransactionRecord($transactionId, [
                    'status' => 'token_failed',
                    'error_message' => json_encode($errorData),
                    'response_data' => $errorData,
                ]);
                
                // Backup the error data
                $this->backupToFile('apple_pay_token_error', [
                    'transaction_id' => $transactionId,
                    'error' => $errorData,
                    'timestamp' => now()->toIso8601String(),
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Failed to process Apple Pay token',
                    'transaction_id' => $transactionId,
                    'error' => $errorData,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception processing Apple Pay token', [
                'transaction_id' => $transactionId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Update the transaction record with the error details
            $this->updateTransactionRecord($transactionId, [
                'status' => 'token_failed',
                'error_message' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'message' => 'An error occurred while processing the Apple Pay token',
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create a charge using a Tap token
     * 
     * @param string $tokenId Tap token ID
     * @param array $chargeData Charge data
     * @param string|null $idempotencyKey Idempotency key for replay protection
     * @return array Response data
     * 
     * @source https://developers.tap.company/docs/apple-pay-token
     */
    public function createCharge($tokenId, $chargeData, $idempotencyKey = null)
    {
        // Validate the charge data
        $validator = Validator::make($chargeData, [
            'amount' => 'required|numeric|min:0.1',
            'currency' => 'required|string|size:3',
            'customer' => 'required|array',
            'customer.first_name' => 'required|string',
            'customer.email' => 'required|email',
            'description' => 'nullable|string',
            'reference' => 'nullable|array',
            'metadata' => 'nullable|array',
            'post' => 'nullable|array',
            'redirect' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Invalid charge data',
                'errors' => $validator->errors()->toArray(),
            ];
        }

        // Check for replay protection if idempotency key is provided
        if ($idempotencyKey) {
            $existingTransaction = $this->findTransactionByIdempotencyKey($idempotencyKey);
            
            if ($existingTransaction) {
                Log::warning('Duplicate transaction attempt detected', [
                    'idempotency_key' => $idempotencyKey,
                    'existing_transaction' => $existingTransaction->transaction_id,
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Duplicate transaction detected',
                    'transaction_id' => $existingTransaction->transaction_id,
                ];
            }
        } else {
            // Generate an idempotency key if not provided
            $idempotencyKey = $this->generateIdempotencyKey();
        }

        // Find the token transaction
        $tokenTransaction = TapPaymentTransaction::where('token_id', $tokenId)->first();
        
        // Generate a unique transaction ID
        $transactionId = $this->generateTransactionId();
        
        // Prepare the request data for Tap API
        $requestData = array_merge($chargeData, [
            'threeDSecure' => true,
            'save_card' => false,
            'source' => [
                'id' => $tokenId,
            ],
        ]);
        
        // Add reference data if not provided
        if (!isset($requestData['reference'])) {
            $requestData['reference'] = [
                'transaction' => $transactionId,
                'order' => 'order_' . Str::random(8),
            ];
        }
        
        // Backup the request data
        $this->backupToFile('charge_request', [
            'transaction_id' => $transactionId,
            'token_id' => $tokenId,
            'request' => $requestData,
            'timestamp' => now()->toIso8601String(),
        ]);
        
        try {
            // Create a transaction record in pending state
            $transaction = $this->createTransactionRecord([
                'transaction_id' => $transactionId,
                'token_id' => $tokenId,
                'amount' => $chargeData['amount'],
                'currency' => $chargeData['currency'],
                'status' => 'charge_pending',
                'payment_method' => $tokenTransaction ? $tokenTransaction->payment_method : 'APPLEPAY',
                'payment_type' => $tokenTransaction ? $tokenTransaction->payment_type : null,
                'card_brand' => $tokenTransaction ? $tokenTransaction->card_brand : null,
                'card_last_four' => $tokenTransaction ? $tokenTransaction->card_last_four : null,
                'card_first_six' => $tokenTransaction ? $tokenTransaction->card_first_six : null,
                'user_id' => auth()->id(),
                'request_data' => $requestData,
                'idempotency_key' => $idempotencyKey,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'is_live' => $this->liveMode,
            ]);
            
            // Make the API request to Tap
            $response = $this->makeRequest('post', 'charges', $requestData);
            
            // Process the response
            if ($response->successful()) {
                $responseData = $response->json();
                
                // Update the transaction record with the charge details
                $this->updateTransactionRecord($transactionId, [
                    'charge_id' => $responseData['id'] ?? null,
                    'status' => strtolower($responseData['status'] ?? 'pending'),
                    'response_data' => $responseData,
                ]);
                
                // Backup the response data
                $this->backupToFile('charge_response', [
                    'transaction_id' => $transactionId,
                    'response' => $responseData,
                    'timestamp' => now()->toIso8601String(),
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Charge created successfully',
                    'transaction_id' => $transactionId,
                    'charge_id' => $responseData['id'],
                    'status' => $responseData['status'],
                    'payment_url' => $responseData['transaction']['url'] ?? null,
                ];
            } else {
                $errorData = $response->json();
                
                // Update the transaction record with the error details
                $this->updateTransactionRecord($transactionId, [
                    'status' => 'charge_failed',
                    'error_message' => json_encode($errorData),
                    'response_data' => $errorData,
                ]);
                
                // Backup the error data
                $this->backupToFile('charge_error', [
                    'transaction_id' => $transactionId,
                    'error' => $errorData,
                    'timestamp' => now()->toIso8601String(),
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Failed to create charge',
                    'transaction_id' => $transactionId,
                    'error' => $errorData,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception creating charge', [
                'transaction_id' => $transactionId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Update the transaction record with the error details
            $this->updateTransactionRecord($transactionId, [
                'status' => 'charge_failed',
                'error_message' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'message' => 'An error occurred while creating the charge',
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ];
        }
    }
}
