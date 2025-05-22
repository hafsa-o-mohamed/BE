<?php

namespace App\Services\TapPayment;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Service for Apple Pay integration with Tap Payments
 * 
 * This service handles Apple Pay token processing
 * Source: https://developers.tap.company/docs/apple-pay-token
 */
class ApplePayService extends TapPaymentService
{
    /**
     * Process Apple Pay token and create a Tap token
     * 
     * @param array $tokenData Apple Pay token data
     * @param string $clientIp Client IP address
     * @return array Response with token_id and card information
     * @throws \Exception If token creation fails
     */
    public function createToken(array $tokenData, string $clientIp)
    {
        // Validate Apple Pay token data
        $this->validateApplePayToken($tokenData);

        // Prepare the request payload
        $payload = [
            'type' => 'applepay',
            'token_data' => $tokenData,
            'client_ip' => $clientIp
        ];

        try {
            // Backup the request data
            $this->backupToFile('apple_pay_token_request', $payload);

            // Make the API request to create a token
            $response = $this->httpClient()->post($this->apiUrl . '/tokens', $payload);
            
            // Get the response data
            $responseData = $response->json();
            
            // Backup the response data
            $this->backupToFile('apple_pay_token_response', $responseData);

            // Check if the request was successful
            if (!$response->successful()) {
                $errorMessage = $responseData['message'] ?? 'Failed to create token';
                $errorCode = $responseData['code'] ?? 'unknown';
                
                Log::error('Tap Payment token creation failed', [
                    'error' => $responseData,
                    'request' => $payload
                ]);
                
                throw new \Exception("Token creation failed: {$errorMessage} (Code: {$errorCode})");
            }

            return $responseData;
        } catch (\Exception $e) {
            Log::error('Exception during Tap Payment token creation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }



    /**
     * Process Apple Pay payment in one step (token creation + charge)
     * 
     * @param array $data Payment data including Apple Pay token
     * @return array Response with charge information
     * @throws \Exception If payment processing fails
     */
    public function processApplePayPayment(array $data)
    {
        try {
            // Validate required data
            $this->validateApplePayData($data);
            
            // Extract Apple Pay token data
            $applePayToken = $data['token_data'];
            $clientIp = $data['ip_address'] ?? request()->ip();
            
            // Create a Tap token from the Apple Pay token
            $tokenResponse = $this->createToken($applePayToken, $clientIp);
            
            // Extract the token ID
            $tokenId = $tokenResponse['id'] ?? null;
            
            if (!$tokenId) {
                throw new \Exception('Failed to obtain token ID from Tap Payments');
            }
            
            // Set payment method to APPLEPAY
            $data['payment_method'] = 'APPLEPAY';
            
            // Add card information from token response if available
            if (isset($tokenResponse['card'])) {
                $data['card_info'] = $tokenResponse['card'];
            }
            
            // Create a charge using the token (using parent method)
            return parent::createCharge($data, $tokenId);
        } catch (\Exception $e) {
            Log::error('Apple Pay payment processing failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Validate Apple Pay token data
     * 
     * @param array $tokenData
     * @throws \InvalidArgumentException If validation fails
     */
    protected function validateApplePayToken(array $tokenData)
    {
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
            $errors = $validator->errors()->toArray();
            Log::error('Apple Pay token validation failed', ['errors' => $errors]);
            throw new ValidationException($validator);
        }
    }

    /**
     * Validate Apple Pay payment data
     * 
     * @param array $data
     * @throws \InvalidArgumentException If validation fails
     */
    protected function validateApplePayData(array $data)
    {
        $validator = Validator::make($data, [
            'amount' => 'required|numeric|min:0.1',
            'currency' => 'sometimes|string|size:3',
            'description' => 'sometimes|string|max:255',
            'customer_name' => 'sometimes|string|max:255',
            'customer_email' => 'sometimes|email|max:255',
            'customer_phone' => 'sometimes|string|max:20',
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
            $errors = $validator->errors()->toArray();
            Log::error('Apple Pay data validation failed', ['errors' => $errors]);
            throw new ValidationException($validator);
        }
    }
}
