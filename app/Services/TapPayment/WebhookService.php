<?php

namespace App\Services\TapPayment;

use App\Models\TapPaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Service for handling Tap Payments webhooks
 * 
 * This service processes webhook notifications from Tap Payments
 * Source: https://developers.tap.company/docs/webhooks
 */
class WebhookService extends TapPaymentService
{
    /**
     * Process webhook from Tap Payments
     * 
     * @param Request $request
     * @return array
     */
    public function processWebhook(Request $request)
    {
        // Get the webhook payload
        $payload = $request->all();
        
        // Verify webhook signature if available
        if ($request->header('Tap-Signature')) {
            $this->verifySignature($request);
        }
        
        // Backup webhook data to file
        $this->backupToFile('webhook_received', [
            'payload' => $payload,
            'headers' => $request->headers->all(),
            'timestamp' => now()->toIso8601String()
        ]);

        try {
            // Process webhook based on event type
            $eventType = $payload['event'] ?? 'unknown';
            $eventId = $payload['id'] ?? null;
            
            // Log webhook receipt
            Log::info('Tap Payment webhook received', [
                'event_type' => $eventType,
                'event_id' => $eventId
            ]);
            
            // Process different event types
            switch ($eventType) {
                case 'charge.succeeded':
                    return $this->handleSuccessfulCharge($payload);
                    
                case 'charge.failed':
                    return $this->handleFailedCharge($payload);
                    
                default:
                    // Log unknown event type
                    Log::info('Tap Payment webhook received with unknown event type', [
                        'event_type' => $eventType,
                        'payload' => $payload
                    ]);
                    
                    return [
                        'status' => 'received',
                        'message' => 'Unhandled event type: ' . $eventType
                    ];
            }
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error processing Tap Payment webhook', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $payload
            ]);
            
            return [
                'status' => 'error',
                'message' => 'Error processing webhook: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify webhook signature
     * 
     * @param Request $request
     * @return bool
     * @throws \Exception If signature verification fails
     */
    protected function verifySignature(Request $request)
    {
        // This is a placeholder for signature verification
        // Tap Payments documentation doesn't provide clear details on signature verification
        // Implement this when Tap provides more information
        
        $signature = $request->header('Tap-Signature');
        
        // Log the signature for now
        Log::info('Tap Payment webhook signature received', [
            'signature' => $signature
        ]);
        
        return true;
    }

    /**
     * Handle successful charge
     * 
     * @param array $payload
     * @return array
     */
    protected function handleSuccessfulCharge($payload)
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
        
        // Update transaction in database
        if ($chargeId) {
            $transaction = $this->findTransactionByChargeId($chargeId);
            
            if (!$transaction && $transactionId) {
                $transaction = $this->findTransaction($transactionId);
            }
            
            if ($transaction) {
                // Extract card information if available
                $cardInfo = $payload['data']['card'] ?? null;
                $cardBrand = $cardInfo['brand'] ?? $transaction->card_brand;
                $cardLastFour = $cardInfo['last_four'] ?? $transaction->card_last_four;
                
                $transaction->update([
                    'charge_id' => $chargeId,
                    'status' => 'successful',
                    'card_brand' => $cardBrand,
                    'card_last_four' => $cardLastFour,
                    'webhook_payload' => $payload
                ]);
                
                // TODO: Trigger any necessary business logic (e.g., update order status, send confirmation email)
            } else {
                // Create a new transaction record if it doesn't exist
                $this->createTransaction([
                    'transaction_id' => $transactionId ?? $this->generateTransactionId(),
                    'charge_id' => $chargeId,
                    'amount' => $amount,
                    'currency' => $currency,
                    'status' => 'successful',
                    'webhook_payload' => $payload
                ]);
            }
        }
        
        return [
            'status' => 'success',
            'message' => 'Charge succeeded webhook processed',
            'charge_id' => $chargeId
        ];
    }

    /**
     * Handle failed charge
     * 
     * @param array $payload
     * @return array
     */
    protected function handleFailedCharge($payload)
    {
        // Extract charge data
        $chargeId = $payload['data']['id'] ?? null;
        $amount = $payload['data']['amount'] ?? 0;
        $currency = $payload['data']['currency'] ?? 'KWD';
        $customerId = $payload['data']['customer']['id'] ?? null;
        $transactionId = $payload['data']['reference']['transaction'] ?? null;
        $orderId = $payload['data']['reference']['order'] ?? null;
        $failureReason = $payload['data']['response']['message'] ?? 'Unknown reason';
        $failureCode = $payload['data']['response']['code'] ?? 'unknown';
        
        // Log failed payment
        Log::error('Tap Payment failed', [
            'charge_id' => $chargeId,
            'amount' => $amount,
            'currency' => $currency,
            'transaction_id' => $transactionId,
            'order_id' => $orderId,
            'failure_reason' => $failureReason,
            'failure_code' => $failureCode
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
            'failure_code' => $failureCode,
            'payload' => $payload,
            'timestamp' => now()->toIso8601String()
        ]);
        
        // Update transaction in database
        if ($chargeId) {
            $transaction = $this->findTransactionByChargeId($chargeId);
            
            if (!$transaction && $transactionId) {
                $transaction = $this->findTransaction($transactionId);
            }
            
            if ($transaction) {
                $transaction->update([
                    'charge_id' => $chargeId,
                    'status' => 'failed',
                    'error_code' => $failureCode,
                    'error_message' => $failureReason,
                    'webhook_payload' => $payload
                ]);
                
                // TODO: Trigger any necessary business logic (e.g., notify customer, retry payment)
            } else {
                // Create a new transaction record if it doesn't exist
                $this->createTransaction([
                    'transaction_id' => $transactionId ?? $this->generateTransactionId(),
                    'charge_id' => $chargeId,
                    'amount' => $amount,
                    'currency' => $currency,
                    'status' => 'failed',
                    'error_code' => $failureCode,
                    'error_message' => $failureReason,
                    'webhook_payload' => $payload
                ]);
            }
        }
        
        return [
            'status' => 'success',
            'message' => 'Charge failed webhook processed',
            'charge_id' => $chargeId
        ];
    }
}
