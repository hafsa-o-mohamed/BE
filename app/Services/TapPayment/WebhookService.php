<?php

namespace App\Services\TapPayment;

use App\Models\TapPaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Service for handling Tap Payments webhooks
 * 
 * @source https://developers.tap.company/docs/webhooks
 */
class WebhookService extends TapPaymentService
{
    /**
     * Process a webhook from Tap Payments
     * 
     * @param Request $request
     * @return array
     */
    public function processWebhook(Request $request)
    {
        // Get the webhook payload
        $payload = $request->all();
        
        // Verify webhook signature if available
        $isVerified = $this->verifyWebhookSignature($request);
        
        // Backup webhook data to file
        $this->backupToFile('webhook_received', [
            'payload' => $payload,
            'headers' => $request->headers->all(),
            'is_verified' => $isVerified,
            'timestamp' => now()->toIso8601String()
        ]);
        
        // Log webhook receipt
        Log::info('Tap Payment webhook received', [
            'event' => $payload['event'] ?? 'unknown',
            'is_verified' => $isVerified,
        ]);
        
        try {
            // Process webhook based on event type
            $eventType = $payload['event'] ?? 'unknown';
            
            switch ($eventType) {
                case 'charge.succeeded':
                    return $this->handleSuccessfulCharge($payload);
                    
                case 'charge.failed':
                    return $this->handleFailedCharge($payload);
                    
                case 'token.created':
                    return $this->handleTokenCreated($payload);
                    
                case 'token.updated':
                    return $this->handleTokenUpdated($payload);
                    
                default:
                    // Log unknown event type
                    Log::info('Tap Payment webhook received with unknown event type', [
                        'event_type' => $eventType,
                    ]);
                    
                    return [
                        'success' => true,
                        'message' => 'Webhook received but event type not handled',
                        'event_type' => $eventType,
                    ];
            }
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error processing Tap Payment webhook', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'event' => $payload['event'] ?? 'unknown',
            ]);
            
            return [
                'success' => false,
                'message' => 'Error processing webhook',
                'error' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * Verify the webhook signature
     * 
     * @param Request $request
     * @return bool
     */
    protected function verifyWebhookSignature(Request $request)
    {
        // Check if signature is present
        $signature = $request->header('Tap-Signature');
        
        if (!$signature) {
            return false;
        }
        
        // TODO: Implement signature verification when Tap provides documentation
        // For now, we'll just log that we received a signature
        Log::info('Tap Payment webhook signature received', [
            'signature' => $signature,
        ]);
        
        return true;
    }
    
    /**
     * Handle a successful charge webhook
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
        
        // Update transaction record if we can find it
        if ($transactionId) {
            $transaction = $this->findTransaction($transactionId);
            
            if ($transaction) {
                $transaction->update([
                    'charge_id' => $chargeId,
                    'status' => 'captured',
                    'response_data' => $payload['data'],
                ]);
            } else {
                // If we can't find the transaction by our ID, try by charge ID
                $transaction = TapPaymentTransaction::where('charge_id', $chargeId)->first();
                
                if ($transaction) {
                    $transaction->update([
                        'status' => 'captured',
                        'response_data' => $payload['data'],
                    ]);
                } else {
                    // Create a new transaction record if we can't find an existing one
                    $this->createTransactionRecord([
                        'transaction_id' => $transactionId ?? 'webhook_' . Str::random(8),
                        'charge_id' => $chargeId,
                        'amount' => $amount,
                        'currency' => $currency,
                        'status' => 'captured',
                        'response_data' => $payload['data'],
                    ]);
                }
            }
        }
        
        // TODO: Trigger any business logic for successful payments
        // For example, update order status, send confirmation email, etc.
        
        return [
            'success' => true,
            'message' => 'Successful charge webhook processed',
            'charge_id' => $chargeId,
        ];
    }
    
    /**
     * Handle a failed charge webhook
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
        
        // Update transaction record if we can find it
        if ($transactionId) {
            $transaction = $this->findTransaction($transactionId);
            
            if ($transaction) {
                $transaction->update([
                    'charge_id' => $chargeId,
                    'status' => 'failed',
                    'error_message' => $failureReason,
                    'response_data' => $payload['data'],
                ]);
            } else {
                // If we can't find the transaction by our ID, try by charge ID
                $transaction = TapPaymentTransaction::where('charge_id', $chargeId)->first();
                
                if ($transaction) {
                    $transaction->update([
                        'status' => 'failed',
                        'error_message' => $failureReason,
                        'response_data' => $payload['data'],
                    ]);
                } else {
                    // Create a new transaction record if we can't find an existing one
                    $this->createTransactionRecord([
                        'transaction_id' => $transactionId ?? 'webhook_' . Str::random(8),
                        'charge_id' => $chargeId,
                        'amount' => $amount,
                        'currency' => $currency,
                        'status' => 'failed',
                        'error_message' => $failureReason,
                        'response_data' => $payload['data'],
                    ]);
                }
            }
        }
        
        // TODO: Trigger any business logic for failed payments
        // For example, notify customer, retry payment, etc.
        
        return [
            'success' => true,
            'message' => 'Failed charge webhook processed',
            'charge_id' => $chargeId,
        ];
    }
    
    /**
     * Handle a token created webhook
     * 
     * @param array $payload
     * @return array
     */
    protected function handleTokenCreated($payload)
    {
        // Extract token data
        $tokenId = $payload['data']['id'] ?? null;
        $tokenType = $payload['data']['type'] ?? null;
        $cardInfo = $payload['data']['card'] ?? null;
        
        // Log token creation
        Log::info('Tap Payment token created', [
            'token_id' => $tokenId,
            'token_type' => $tokenType,
        ]);
        
        // Backup token data
        $this->backupToFile('token_created', [
            'token_id' => $tokenId,
            'token_type' => $tokenType,
            'card_info' => $cardInfo,
            'payload' => $payload,
            'timestamp' => now()->toIso8601String()
        ]);
        
        // Find transaction by token ID
        $transaction = TapPaymentTransaction::where('token_id', $tokenId)->first();
        
        if ($transaction) {
            // Update transaction with token details
            $transaction->update([
                'status' => 'token_created',
                'payment_method' => $tokenType,
                'payment_type' => $cardInfo['funding'] ?? null,
                'card_brand' => $cardInfo['brand'] ?? null,
                'card_last_four' => $cardInfo['last_four'] ?? null,
                'card_first_six' => $cardInfo['first_six'] ?? null,
                'response_data' => $payload['data'],
            ]);
        }
        
        return [
            'success' => true,
            'message' => 'Token created webhook processed',
            'token_id' => $tokenId,
        ];
    }
    
    /**
     * Handle a token updated webhook
     * 
     * @param array $payload
     * @return array
     */
    protected function handleTokenUpdated($payload)
    {
        // Extract token data
        $tokenId = $payload['data']['id'] ?? null;
        $tokenType = $payload['data']['type'] ?? null;
        $cardInfo = $payload['data']['card'] ?? null;
        $used = $payload['data']['used'] ?? false;
        
        // Log token update
        Log::info('Tap Payment token updated', [
            'token_id' => $tokenId,
            'token_type' => $tokenType,
            'used' => $used,
        ]);
        
        // Backup token data
        $this->backupToFile('token_updated', [
            'token_id' => $tokenId,
            'token_type' => $tokenType,
            'card_info' => $cardInfo,
            'used' => $used,
            'payload' => $payload,
            'timestamp' => now()->toIso8601String()
        ]);
        
        // Find transaction by token ID
        $transaction = TapPaymentTransaction::where('token_id', $tokenId)->first();
        
        if ($transaction) {
            // Update transaction with token details
            $transaction->update([
                'response_data' => $payload['data'],
            ]);
        }
        
        return [
            'success' => true,
            'message' => 'Token updated webhook processed',
            'token_id' => $tokenId,
        ];
    }
}
