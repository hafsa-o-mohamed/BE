<?php

namespace App\Services\TapPayment;

use App\Models\TapPaymentTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Base service for Tap Payments integration
 * 
 * This service handles common functionality for Tap Payments API
 * 
 * @source https://developers.tap.company/docs
 */
class TapPaymentService
{
    /**
     * Tap API base URL
     * 
     * @var string
     */
    protected $apiUrl;
    
    /**
     * Tap API secret key
     * 
     * @var string
     */
    protected $secretKey;
    
    /**
     * Directory for backup files
     * 
     * @var string
     */
    protected $backupDir = 'tap_payments_backup';
    
    /**
     * Whether to use live mode or test mode
     * 
     * @var bool
     */
    protected $liveMode;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->apiUrl = rtrim(env('TAP_API_URL', 'https://api.tap.company/v2'), '/');
        $this->secretKey = env('TAP_SECRET_KEY');
        $this->liveMode = env('TAP_LIVE_MODE', false);
        
        // Ensure backup directory exists
        if (!Storage::exists($this->backupDir)) {
            Storage::makeDirectory($this->backupDir);
        }
    }

    /**
     * Make a request to Tap API
     * 
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $endpoint API endpoint
     * @param array $data Request data
     * @param array $headers Additional headers
     * @return \Illuminate\Http\Client\Response
     */
    protected function makeRequest($method, $endpoint, $data = [], $headers = [])
    {
        $url = $this->apiUrl . '/' . ltrim($endpoint, '/');
        
        $defaultHeaders = [
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ];
        
        $mergedHeaders = array_merge($defaultHeaders, $headers);
        
        try {
            $response = Http::withHeaders($mergedHeaders)->$method($url, $data);
            
            // Log API request and response for debugging
            Log::debug('Tap API Request', [
                'method' => $method,
                'url' => $url,
                'headers' => $mergedHeaders,
                'data' => $data,
                'response_status' => $response->status(),
                'response_body' => $response->json(),
            ]);
            
            return $response;
        } catch (\Exception $e) {
            Log::error('Tap API Request Failed', [
                'method' => $method,
                'url' => $url,
                'headers' => $mergedHeaders,
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
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
     * Generate a unique idempotency key for replay protection
     * 
     * @return string
     */
    protected function generateIdempotencyKey()
    {
        return 'idkey_' . Str::uuid();
    }

    /**
     * Backup data to file as a safeguard
     * 
     * @param string $type Type of data being backed up
     * @param array $data Data to backup
     * @return bool Whether backup was successful
     */
    protected function backupToFile($type, $data)
    {
        try {
            // Create a unique filename
            $filename = $type . '_' . now()->format('Y-m-d_H-i-s') . '_' . Str::random(8) . '.txt';
            
            // Write data to file
            Storage::put(
                $this->backupDir . '/' . $filename, 
                json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to backup Tap Payment data to file', [
                'message' => $e->getMessage(),
                'type' => $type,
                'trace' => $e->getTraceAsString(),
            ]);
            
            return false;
        }
    }

    /**
     * Create a transaction record in the database
     * 
     * @param array $data Transaction data
     * @return TapPaymentTransaction
     */
    protected function createTransactionRecord($data)
    {
        return TapPaymentTransaction::create($data);
    }

    /**
     * Update a transaction record in the database
     * 
     * @param string $transactionId Transaction ID
     * @param array $data Data to update
     * @return TapPaymentTransaction|null
     */
    protected function updateTransactionRecord($transactionId, $data)
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
     * @param string $transactionId Transaction ID
     * @return TapPaymentTransaction|null
     */
    protected function findTransaction($transactionId)
    {
        return TapPaymentTransaction::where('transaction_id', $transactionId)->first();
    }

    /**
     * Find a transaction by its idempotency key
     * 
     * @param string $idempotencyKey Idempotency key
     * @return TapPaymentTransaction|null
     */
    protected function findTransactionByIdempotencyKey($idempotencyKey)
    {
        return TapPaymentTransaction::where('idempotency_key', $idempotencyKey)->first();
    }
}
