<?php

namespace App\Services\TapPayment;

use App\Models\TapPaymentTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Service for handling payment agreements with Tap Payments
 * 
 * @source https://developers.tap.company/docs/payment-agreement
 * @source https://developers.tap.company/docs/creating-payment-agreement
 * @source https://developers.tap.company/docs/merchant-initiated-transaction
 */
class PaymentAgreementService extends TapPaymentService
{
    /**
     * Create a payment agreement
     * 
     * @param string $tokenId Tap token ID
     * @param array $agreementData Agreement data
     * @param string|null $idempotencyKey Idempotency key for replay protection
     * @return array Response data
     * 
     * @source https://developers.tap.company/docs/creating-payment-agreement
     */
    public function createAgreement($tokenId, $agreementData, $idempotencyKey = null)
    {
        // Validate the agreement data
        $validator = Validator::make($agreementData, [
            'contract_type' => 'required|string|in:UNSCHEDULED,SUBSCRIPTION,INSTALLMENT,MILESTONE,ORDER',
            'customer' => 'required|array',
            'customer.first_name' => 'required|string',
            'customer.email' => 'required|email',
            'currency' => 'required|string|size:3',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Invalid agreement data',
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
            'contract' => [
                'type' => $agreementData['contract_type'],
            ],
            'customer' => $agreementData['customer'],
            'currency' => $agreementData['currency'],
            'metadata' => $agreementData['metadata'] ?? [],
            'source' => [
                'id' => $tokenId,
            ],
        ];
        
        // Backup the request data
        $this->backupToFile('agreement_request', [
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
                'status' => 'agreement_pending',
                'request_data' => $requestData,
                'idempotency_key' => $idempotencyKey,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'is_live' => $this->liveMode,
            ]);
            
            // Make the API request to Tap
            $response = $this->makeRequest('post', 'payment_agreements', $requestData);
            
            // Process the response
            if ($response->successful()) {
                $responseData = $response->json();
                
                // Update the transaction record with the agreement details
                $this->updateTransactionRecord($transactionId, [
                    'payment_agreement_id' => $responseData['id'] ?? null,
                    'status' => 'agreement_created',
                    'response_data' => $responseData,
                ]);
                
                // Backup the response data
                $this->backupToFile('agreement_response', [
                    'transaction_id' => $transactionId,
                    'response' => $responseData,
                    'timestamp' => now()->toIso8601String(),
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Payment agreement created successfully',
                    'transaction_id' => $transactionId,
                    'agreement_id' => $responseData['id'],
                    'agreement' => $responseData,
                ];
            } else {
                $errorData = $response->json();
                
                // Update the transaction record with the error details
                $this->updateTransactionRecord($transactionId, [
                    'status' => 'agreement_failed',
                    'error_message' => json_encode($errorData),
                    'response_data' => $errorData,
                ]);
                
                // Backup the error data
                $this->backupToFile('agreement_error', [
                    'transaction_id' => $transactionId,
                    'error' => $errorData,
                    'timestamp' => now()->toIso8601String(),
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Failed to create payment agreement',
                    'transaction_id' => $transactionId,
                    'error' => $errorData,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception creating payment agreement', [
                'transaction_id' => $transactionId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Update the transaction record with the error details
            $this->updateTransactionRecord($transactionId, [
                'status' => 'agreement_failed',
                'error_message' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'message' => 'An error occurred while creating the payment agreement',
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create a merchant-initiated charge using a payment agreement
     * 
     * @param string $agreementId Payment agreement ID
     * @param array $chargeData Charge data
     * @param string|null $idempotencyKey Idempotency key for replay protection
     * @return array Response data
     * 
     * @source https://developers.tap.company/docs/merchant-initiated-transaction
     */
    public function createMerchantInitiatedCharge($agreementId, $chargeData, $idempotencyKey = null)
    {
        // Validate the charge data
        $validator = Validator::make($chargeData, [
            'amount' => 'required|numeric|min:0.1',
            'currency' => 'required|string|size:3',
            'customer_id' => 'required|string',
            'token_id' => 'required|string',
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

        // Generate a unique transaction ID
        $transactionId = $this->generateTransactionId();
        
        // Prepare the request data for Tap API
        $requestData = [
            'amount' => $chargeData['amount'],
            'currency' => $chargeData['currency'],
            'payment_agreement' => [
                'id' => $agreementId,
            ],
            'customer_initiated' => false,
            'threeDSecure' => false,
            'save_card' => false,
            'description' => $chargeData['description'] ?? 'Merchant initiated charge',
            'statement_descriptor' => $chargeData['statement_descriptor'] ?? 'Merchant charge',
            'metadata' => $chargeData['metadata'] ?? [],
            'reference' => $chargeData['reference'] ?? [
                'transaction' => $transactionId,
                'order' => 'order_' . Str::random(8),
            ],
            'receipt' => $chargeData['receipt'] ?? [
                'email' => true,
                'sms' => false,
            ],
            'customer' => [
                'id' => $chargeData['customer_id'],
            ],
            'source' => [
                'id' => $chargeData['token_id'],
            ],
            'post' => $chargeData['post'] ?? null,
            'redirect' => $chargeData['redirect'] ?? null,
        ];
        
        // Backup the request data
        $this->backupToFile('merchant_charge_request', [
            'transaction_id' => $transactionId,
            'agreement_id' => $agreementId,
            'request' => $requestData,
            'timestamp' => now()->toIso8601String(),
        ]);
        
        try {
            // Create a transaction record in pending state
            $transaction = $this->createTransactionRecord([
                'transaction_id' => $transactionId,
                'token_id' => $chargeData['token_id'],
                'payment_agreement_id' => $agreementId,
                'amount' => $chargeData['amount'],
                'currency' => $chargeData['currency'],
                'status' => 'charge_pending',
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
                $this->backupToFile('merchant_charge_response', [
                    'transaction_id' => $transactionId,
                    'response' => $responseData,
                    'timestamp' => now()->toIso8601String(),
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Merchant initiated charge created successfully',
                    'transaction_id' => $transactionId,
                    'charge_id' => $responseData['id'],
                    'status' => $responseData['status'],
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
                $this->backupToFile('merchant_charge_error', [
                    'transaction_id' => $transactionId,
                    'error' => $errorData,
                    'timestamp' => now()->toIso8601String(),
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Failed to create merchant initiated charge',
                    'transaction_id' => $transactionId,
                    'error' => $errorData,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception creating merchant initiated charge', [
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
                'message' => 'An error occurred while creating the merchant initiated charge',
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ];
        }
    }
}
