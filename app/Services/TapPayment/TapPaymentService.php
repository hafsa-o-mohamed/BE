<?php

namespace App\Services\TapPayment;

use App\Models\TapPaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Base service for Tap Payments integration
 * 
 * This service handles common functionality for Tap Payments API interactions
 * Source: https://developers.tap.company/docs
 */
class TapPaymentService
{
    /**
     * Tap API base URL
     * 
     * @var string
     */
    protected $apiUrl = 'https://api.tap.company/v2';

    /**
     * Directory for backup files
     * 
     * @var string
     */
    protected $backupDir = 'tap_payments_backup';

    /**
     * Constructor
     */
    public function __construct()
    {
        // Check if we're in sandbox mode
        if (config('services.tap.sandbox', true)) {
            $this->apiUrl = 'https://api.tap.company/v2';
        }
    }

    /**
     * Get the API key from configuration
     * 
     * @return string
     */
    protected function getApiKey()
    {
        return config('services.tap.secret_key', env('TAP_SECRET_KEY'));
    }

    /**
     * Create HTTP client with proper headers
     * 
     * @return \Illuminate\Http\Client\PendingRequest
     */
    protected function httpClient()
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getApiKey(),
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Generate a unique transaction ID
     * 
     * @return string
     */
    protected function generateTransactionId()
    {
        return 'txn_' . Str::uuid();
    }

    /**
     * Backup data to file as a safeguard
     * 
     * @param string $type
     * @param array $data
     * @return bool
     */
    protected function backupToFile($type, $data)
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

    /**
     * Create a new transaction record
     * 
     * @param array $data
     * @return \App\Models\TapPaymentTransaction
     */
    protected function createTransaction(array $data)
    {
        // Generate transaction ID if not provided
        if (!isset($data['transaction_id'])) {
            $data['transaction_id'] = $this->generateTransactionId();
        }

        return TapPaymentTransaction::create($data);
    }

    /**
     * Update an existing transaction record
     * 
     * @param string $transactionId
     * @param array $data
     * @return \App\Models\TapPaymentTransaction|null
     */
    protected function updateTransaction(string $transactionId, array $data)
    {
        $transaction = TapPaymentTransaction::where('transaction_id', $transactionId)->first();
        
        if ($transaction) {
            $transaction->update($data);
            return $transaction->fresh();
        }
        
        return null;
    }

    /**
     * Find a transaction by its ID
     * 
     * @param string $transactionId
     * @return \App\Models\TapPaymentTransaction|null
     */
    protected function findTransaction(string $transactionId)
    {
        return TapPaymentTransaction::where('transaction_id', $transactionId)->first();
    }

    /**
     * Find a transaction by its charge ID
     * 
     * @param string $chargeId
     * @return \App\Models\TapPaymentTransaction|null
     */
    protected function findTransactionByChargeId(string $chargeId)
    {
        return TapPaymentTransaction::where('charge_id', $chargeId)->first();
    }
    
    /**
     * Create a charge using a Tap token
     * 
     * @param array $data Charge data
     * @param string $tokenId Tap token ID
     * @return array Response with charge information
     * @throws \Exception If charge creation fails
     */
    public function createCharge(array $data, string $tokenId)
    {
        // Generate a unique transaction ID
        $transactionId = $this->generateTransactionId();
        
        // Prepare charge data
        $chargeData = [
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'KWD',
            'threeDSecure' => true,
            'save_card' => false,
            'description' => $data['description'] ?? 'Payment',
            'statement_descriptor' => 'Tap Payment',
            'reference' => [
                'transaction' => $transactionId,
                'order' => $data['reference_id'] ?? $transactionId
            ],
            'metadata' => $data['metadata'] ?? ['transaction_id' => $transactionId],
            'customer' => [
                'first_name' => $data['customer_name'] ?? 'Customer',
                'email' => $data['customer_email'] ?? '',
                'phone' => [
                    'country_code' => $data['country_code'] ?? '965',
                    'number' => $data['customer_phone'] ?? ''
                ]
            ],
            'source' => [
                'id' => $tokenId
            ],
            'post' => [
                'url' => config('services.tap.webhook_url', env('TAP_WEBHOOK_URL'))
            ],
            'redirect' => [
                'url' => config('services.tap.redirect_url', env('TAP_REDIRECT_URL'))
            ]
        ];

        try {
            // Create a transaction record in the database
            $transaction = $this->createTransaction([
                'transaction_id' => $transactionId,
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'KWD',
                'description' => $data['description'] ?? 'Payment',
                'reference_id' => $data['reference_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'payment_method' => $data['payment_method'] ?? 'CARD',
                'token_id' => $tokenId,
                'status' => 'pending',
                'request_payload' => $chargeData,
                'ip_address' => $data['ip_address'] ?? null,
                'user_id' => $data['user_id'] ?? null,
            ]);

            // Backup the request data
            $this->backupToFile('charge_request', [
                'transaction_id' => $transactionId,
                'payload' => $chargeData
            ]);

            // Make the API request to create a charge
            $response = $this->httpClient()->post($this->apiUrl . '/charges', $chargeData);
            
            // Get the response data
            $responseData = $response->json();
            
            // Backup the response data
            $this->backupToFile('charge_response', [
                'transaction_id' => $transactionId,
                'response' => $responseData
            ]);

            // Check if the request was successful
            if (!$response->successful()) {
                $errorMessage = $responseData['message'] ?? 'Failed to create charge';
                $errorCode = $responseData['code'] ?? 'unknown';
                
                // Update transaction with error details
                $transaction->update([
                    'status' => 'failed',
                    'error_code' => $errorCode,
                    'error_message' => $errorMessage,
                    'response_payload' => $responseData
                ]);
                
                Log::error('Tap Payment charge creation failed', [
                    'transaction_id' => $transactionId,
                    'error' => $responseData,
                    'request' => $chargeData
                ]);
                
                throw new \Exception("Charge creation failed: {$errorMessage} (Code: {$errorCode})");
            }

            // Extract card information if available
            $cardInfo = $responseData['card'] ?? null;
            $cardBrand = $cardInfo['brand'] ?? null;
            $cardLastFour = $cardInfo['last_four'] ?? null;

            // Update transaction with charge details
            $transaction->update([
                'charge_id' => $responseData['id'] ?? null,
                'status' => $responseData['status'] ?? 'pending',
                'card_brand' => $cardBrand,
                'card_last_four' => $cardLastFour,
                'response_payload' => $responseData
            ]);

            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'charge_id' => $responseData['id'] ?? null,
                'status' => $responseData['status'] ?? 'pending',
                'payment_url' => $responseData['transaction']['url'] ?? null,
                'card' => $cardInfo
            ];
        } catch (\Exception $e) {
            Log::error('Exception during Tap Payment charge creation', [
                'transaction_id' => $transactionId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Validate payment data for charge creation
     * 
     * @param array $data
     * @throws \InvalidArgumentException If validation fails
     */
    protected function validatePaymentData(array $data)
    {
        $validator = Validator::make($data, [
            'amount' => 'required|numeric|min:0.1',
            'currency' => 'sometimes|string|size:3',
            'description' => 'sometimes|string|max:255',
            'customer_name' => 'sometimes|string|max:255',
            'customer_email' => 'sometimes|email|max:255',
            'customer_phone' => 'sometimes|string|max:20',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            Log::error('Payment data validation failed', ['errors' => $errors]);
            throw new ValidationException($validator);
        }
    }
}
