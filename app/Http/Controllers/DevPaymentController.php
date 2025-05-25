<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\DevWebhookLog;

class DevPaymentController extends Controller
{
    /**
     * Tap Payments API Configuration
     */
    private const TAP_API_BASE = 'https://api.tap.company';
    //TAPPAY1_TEST_SECRET_KEY=REDACTED_SECRET_KEY
    // private const SECRET_KEY = config('services.tap.secret_key');
    // TAPPAY1_TEST_PUBLIC_KEY=pk_test_82x9FugWswyJjOp6GiSX0Nk5
    // private const PUBLISHABLE_KEY = config('services.tap.public_key');
    private string $SECRET_KEY;
    private string $PUBLISHABLE_KEY;

    public function __construct()
    {
        $this->SECRET_KEY = config('services.tap.secret_key');
        $this->PUBLISHABLE_KEY = config('services.tap.public_key');
    }

    /**
     * Test cards data based on common payment processor test cards
     * These cards are safe for testing and will not result in actual charges
     */
    private function getTestCards()
    {
        return [
            [
                'name' => 'Visa - Success',
                'number' => '4508750015741019',
                'exp_month' => '01',
                'exp_year' => '2039',
                'cvc' => '100',
                'brand' => 'VISA',
                'scenario' => 'Successful payment',
                'color' => 'bg-green-500'
            ],
            [
                'name' => 'Visa - Insufficient Funds',
                'number' => '4000000000000002',
                'exp_month' => '12',
                'exp_year' => '2030',
                'cvc' => '123',
                'brand' => 'VISA',
                'scenario' => 'Card declined - insufficient funds',
                'color' => 'bg-red-500'
            ],
            [
                'name' => 'Mastercard - Success',
                'number' => '5555555555554444',
                'exp_month' => '12',
                'exp_year' => '2030',
                'cvc' => '123',
                'brand' => 'MASTERCARD',
                'scenario' => 'Successful payment',
                'color' => 'bg-green-500'
            ],
            [
                'name' => 'Mastercard - Generic Decline',
                'number' => '5555555555554477',
                'exp_month' => '12',
                'exp_year' => '2030',
                'cvc' => '123',
                'brand' => 'MASTERCARD',
                'scenario' => 'Card declined - generic decline',
                'color' => 'bg-red-500'
            ],
            [
                'name' => 'American Express - Success',
                'number' => '378282246310005',
                'exp_month' => '12',
                'exp_year' => '2030',
                'cvc' => '1234',
                'brand' => 'AMEX',
                'scenario' => 'Successful payment',
                'color' => 'bg-green-500'
            ],
            [
                'name' => 'Test 3D Secure',
                'number' => '4000000000003220',
                'exp_month' => '12',
                'exp_year' => '2030',
                'cvc' => '123',
                'brand' => 'VISA',
                'scenario' => '3D Secure authentication required',
                'color' => 'bg-yellow-500'
            ]
        ];
    }

    /**
     * Show test cards page
     */
    public function testCards()
    {
        $testCards = $this->getTestCards();
        return view('dev.payments.test-cards', compact('testCards'));
    }

    /**
     * Show token testing page
     * 
     * This page allows testing of Tap Payments Token Creation API
     * Based on the documentation, tokens are single-use and contain credit card details securely
     * They can be used in Charges, Authorize, or Card API calls
     * 
     * Key benefits from Tap Token API:
     * - PCI compliance: Card details are tokenized securely
     * - Single-use: Tokens expire quickly for security
     * - Flexible: Can be used across different payment operations
     * - Secure: Card details never stored on our servers
     */
    public function tokens()
    {
        $testCards = $this->getTestCards();
        return view('dev.payments.tokens', compact('testCards'));
    }

    /**
     * Create a token using Tap Payments API
     * 
     * Implementation based on create-token.md documentation:
     * - Creates single-use tokens for credit cards
     * - Requires card object with number, exp_month, exp_year, cvc, name, address
     * - Optional client_ip for enhanced security
     * - Returns token object with id, created timestamp, card details (masked)
     * 
     * Response includes:
     * - Token ID for future API calls
     * - Card object with masked details (first_six, last_four)
     * - Issuer information and card metadata
     * - Used status and live_mode indicator
     */
    public function createToken(Request $request)
    {
        $request->validate([
            'card_number' => 'required|string',
            'exp_month' => 'required|integer|min:1|max:12',
            'exp_year' => 'required|integer|min:' . date('Y'),
            'cvc' => 'required|string',
            'card_name' => 'required|string',
            'address_country' => 'required|string',
            'address_city' => 'required|string',
            'address_line1' => 'required|string',
        ]);

        try {
            // Prepare the request payload as per Tap API documentation
            $payload = [
                'card' => [
                    'number' => (int) str_replace(' ', '', $request->card_number),
                    'exp_month' => (int) $request->exp_month,
                    'exp_year' => (int) $request->exp_year,
                    'cvc' => (int) $request->cvc,
                    'name' => $request->card_name,
                    'address' => [
                        'country' => $request->address_country,
                        'line1' => $request->address_line1,
                        'city' => $request->address_city,
                        'street' => $request->address_street ?? '',
                        'avenue' => $request->address_avenue ?? '',
                    ]
                ],
                'client_ip' => $request->header('CF-Connecting-IP') ?? $request->ip()
            ];

            // Make actual API call to Tap Payments
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->SECRET_KEY,
                'Content-Type' => 'application/json',
            ])->post(self::TAP_API_BASE . '/v2/tokens', $payload);

            if ($response->successful()) {
                $tokenData = $response->json();
                
                Log::info('Token creation successful', ['payload' => $payload, 'response' => $tokenData]);

                return response()->json([
                    'success' => true,
                    'token' => $tokenData,
                    'debug' => [
                        'payload_sent' => $payload,
                        'note' => 'Real API response from Tap Payments'
                    ]
                ]);
            } else {
                $errorData = $response->json();
                Log::error('Token creation failed', ['error' => $errorData, 'status' => $response->status()]);
                
                return response()->json([
                    'success' => false,
                    'error' => $errorData['errors'] ?? 'Token creation failed',
                    'debug' => [
                        'status' => $response->status(),
                        'response' => $errorData
                    ]
                ], $response->status());
            }

        } catch (\Exception $e) {
            Log::error('Token creation exception', ['error' => $e->getMessage(), 'request' => $request->all()]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show charge testing page
     */
    public function charges()
    {
        return view('dev.payments.charges');
    }

    /**
     * Create a charge using Tap Payments API
     * 
     * Implementation based on create-charge.md documentation:
     * - Initiates charge requests for payment processing
     * - Can use tokens as payment source for security
     * - Supports various payment methods (cards, local payment methods)
     * - Returns comprehensive transaction details
     * 
     * Key parameters:
     * - amount: Decimal amount to charge
     * - currency: ISO currency code
     * - source: Payment method (token, card, etc.)
     * - customer: Customer details
     * - redirect: URLs for payment flow completion
     */
    public function createCharge(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.1',
            'currency' => 'required|string|size:3',
            'token_id' => 'required|string',
            'customer_email' => 'required|email',
            'customer_name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        try {
            // Prepare charge request payload as per Tap API documentation
            $payload = [
                'amount' => (float) $request->amount,
                'currency' => strtoupper($request->currency),
                'customer_initiated' => true,
                'threeDSecure' => true,
                'save_card' => false,
                'description' => $request->description ?? 'Test charge from dev environment',
                'customer' => [
                    'first_name' => explode(' ', $request->customer_name)[0],
                    'last_name' => explode(' ', $request->customer_name)[1] ?? '',
                    'email' => $request->customer_email,
                ],
                'source' => [
                    'id' => $request->token_id
                ],
                'redirect' => [
                    'url' => config('app.url') . route('dev.payments.redirect', [], false) . '?status=completed'
                ],
                'post' => [
                    'url' => config('app.url') . route('dev.payments.webhook', [], false)
                ]
            ];

            // Make actual API call to Tap Payments
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->SECRET_KEY,
                'Content-Type' => 'application/json',
            ])->post(self::TAP_API_BASE . '/v2/charges', $payload);

            if ($response->successful()) {
                $chargeData = $response->json();
                
                Log::info('Charge creation successful', ['payload' => $payload, 'response' => $chargeData]);

                return response()->json([
                    'success' => true,
                    'charge' => $chargeData,
                    'debug' => [
                        'payload_sent' => $payload,
                        'note' => 'Real API response from Tap Payments',
                        'webhook_url' => $payload['post']['url'],
                        'redirect_url' => $payload['redirect']['url']
                    ]
                ]);
            } else {
                $errorData = $response->json();
                Log::error('Charge creation failed', ['error' => $errorData, 'status' => $response->status()]);
                
                return response()->json([
                    'success' => false,
                    'error' => $errorData['errors'] ?? 'Charge creation failed',
                    'debug' => [
                        'status' => $response->status(),
                        'response' => $errorData
                    ]
                ], $response->status());
            }

        } catch (\Exception $e) {
            Log::error('Charge creation exception', ['error' => $e->getMessage(), 'request' => $request->all()]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle Tap Payments webhook notifications
     * 
     * This endpoint receives secure webhook notifications from Tap Payments
     * when payment events occur (captures, failures, etc.)
     * 
     * Security validation using hashstring as per webhook documentation
     */
    public function webhook(Request $request)
    {
        $webhookLog = null;
        
        try {
            $webhookData = $request->all();
            $headers = $request->headers->all();
            
            // Create initial webhook log entry
            $webhookLog = DevWebhookLog::create([
                'webhook_id' => $webhookData['id'] ?? uniqid('webhook_'),
                'object_type' => $webhookData['object'] ?? 'unknown',
                'object_id' => $webhookData['id'] ?? 'unknown',
                'event_status' => $webhookData['status'] ?? 'unknown',
                'amount' => $webhookData['amount'] ?? null,
                'currency' => $webhookData['currency'] ?? null,
                'gateway_reference' => $webhookData['reference']['gateway'] ?? null,
                'payment_reference' => $webhookData['reference']['payment'] ?? null,
                'received_hashstring' => $request->header('hashstring'),
                'webhook_headers' => $headers,
                'webhook_payload' => $webhookData,
                'processing_status' => 'received',
            ]);

            Log::info('Webhook received and logged', [
                'webhook_log_id' => $webhookLog->id,
                'object_type' => $webhookLog->object_type,
                'object_id' => $webhookLog->object_id
            ]);

            // Validate webhook security using hashstring
            $isValid = $this->validateWebhookSecurity($request, $webhookData);
            
            // Update webhook log with validation results
            $webhookLog->update([
                'calculated_hashstring' => $this->lastCalculatedHash ?? null,
                'hash_valid' => $isValid,
                'processing_status' => $isValid ? 'validated' : 'failed',
                'processing_notes' => $isValid ? 'Hash validation passed' : 'Hash validation failed - potential security issue'
            ]);
            
            if (!$isValid) {
                Log::error('Webhook security validation failed', [
                    'webhook_log_id' => $webhookLog->id,
                    'received_hash' => $request->header('hashstring'),
                    'calculated_hash' => $this->lastCalculatedHash
                ]);
                
                return response()->json(['error' => 'Invalid webhook signature'], 403);
            }

            // Process the webhook based on object type
            $objectType = $webhookData['object'] ?? 'unknown';
            $status = $webhookData['status'] ?? 'unknown';
            $id = $webhookData['id'] ?? 'unknown';

            Log::info('Processing webhook', [
                'webhook_log_id' => $webhookLog->id,
                'object_type' => $objectType,
                'status' => $status,
                'id' => $id
            ]);

            // Handle different webhook types
            $processingNotes = '';
            switch ($objectType) {
                case 'charge':
                    $processingNotes = $this->handleChargeWebhook($webhookData);
                    break;
                case 'authorize':
                    $processingNotes = $this->handleAuthorizeWebhook($webhookData);
                    break;
                case 'invoice':
                    $processingNotes = $this->handleInvoiceWebhook($webhookData);
                    break;
                default:
                    $processingNotes = "Unknown webhook object type: {$objectType}";
                    Log::warning('Unknown webhook object type', ['object_type' => $objectType]);
            }

            // Mark as processed
            $webhookLog->update([
                'processing_status' => 'processed',
                'processed_at' => now(),
                'processing_notes' => $processingNotes
            ]);

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'webhook_log_id' => $webhookLog?->id
            ]);
            
            // Update webhook log with error if it exists
            if ($webhookLog) {
                $webhookLog->update([
                    'processing_status' => 'failed',
                    'processing_notes' => 'Exception during processing: ' . $e->getMessage()
                ]);
            }
            
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Show webhook logs page (DEV ONLY)
     */
    public function webhookLogs(Request $request)
    {
        $query = DevWebhookLog::query()->orderBy('created_at', 'desc');
        
        // Apply filters
        if ($request->filled('object_type')) {
            $query->byObjectType($request->object_type);
        }
        
        if ($request->filled('processing_status')) {
            $query->byProcessingStatus($request->processing_status);
        }
        
        if ($request->filled('hours')) {
            $query->recent((int) $request->hours);
        }

        $webhookLogs = $query->paginate(50)->withQueryString();
        
        // Get summary stats
        $stats = [
            'total' => DevWebhookLog::count(),
            'recent_24h' => DevWebhookLog::recent(24)->count(),
            'processed' => DevWebhookLog::byProcessingStatus('processed')->count(),
            'failed' => DevWebhookLog::byProcessingStatus('failed')->count(),
        ];

        return view('dev.payments.webhook-logs', compact('webhookLogs', 'stats'));
    }

    /**
     * Handle payment redirect after successful/failed payments
     */
    public function redirect(Request $request)
    {
        $status = $request->get('status', 'unknown');
        $chargeId = $request->get('tap_id');
        
        Log::info('Payment redirect received', [
            'status' => $status,
            'charge_id' => $chargeId,
            'all_params' => $request->all()
        ]);

        // In a real application, you would:
        // 1. Verify the charge status with Tap API
        // 2. Update your database
        // 3. Redirect user to appropriate success/failure page
        
        return view('dev.payments.redirect', [
            'status' => $status,
            'chargeId' => $chargeId,
            'params' => $request->all()
        ]);
    }

    /**
     * Store calculated hash for logging purposes
     */
    private $lastCalculatedHash = null;

    /**
     * Validate webhook security using hashstring validation
     * Based on webhook-guide.md documentation
     */
    private function validateWebhookSecurity(Request $request, array $webhookData): bool
    {
        $receivedHashString = $request->header('hashstring');
        
        if (!$receivedHashString) {
            Log::warning('No hashstring header found in webhook');
            return false;
        }

        try {
            // Extract required fields based on object type
            $objectType = $webhookData['object'] ?? '';
            
            switch ($objectType) {
                case 'charge':
                case 'authorize':
                    $toBeHashedString = $this->buildHashStringForChargeOrAuthorize($webhookData);
                    break;
                case 'invoice':
                    $toBeHashedString = $this->buildHashStringForInvoice($webhookData);
                    break;
                default:
                    Log::warning('Unknown object type for hash validation', ['object_type' => $objectType]);
                    return false;
            }

            // Create hash using HMAC SHA256 with secret key
            $calculatedHashString = hash_hmac('sha256', $toBeHashedString, $this->SECRET_KEY);
            $this->lastCalculatedHash = $calculatedHashString;
            
            Log::info('Hash validation', [
                'received_hash' => $receivedHashString,
                'calculated_hash' => $calculatedHashString,
                'hash_string_data' => $toBeHashedString,
                'match' => $receivedHashString === $calculatedHashString
            ]);

            return $receivedHashString === $calculatedHashString;

        } catch (\Exception $e) {
            Log::error('Hash validation error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Build hash string for charge or authorize objects
     */
    private function buildHashStringForChargeOrAuthorize(array $data): string
    {
        $id = $data['id'] ?? '';
        $amount = number_format((float)($data['amount'] ?? 0), $this->getDecimalPlaces($data['currency'] ?? 'USD'), '.', '');
        $currency = $data['currency'] ?? '';
        $gatewayReference = $data['reference']['gateway'] ?? '';
        $paymentReference = $data['reference']['payment'] ?? '';
        $status = $data['status'] ?? '';
        $created = $data['transaction']['created'] ?? '';

        return "x_id{$id}x_amount{$amount}x_currency{$currency}x_gateway_reference{$gatewayReference}x_payment_reference{$paymentReference}x_status{$status}x_created{$created}";
    }

    /**
     * Build hash string for invoice objects
     */
    private function buildHashStringForInvoice(array $data): string
    {
        $id = $data['id'] ?? '';
        $amount = number_format((float)($data['amount'] ?? 0), $this->getDecimalPlaces($data['currency'] ?? 'USD'), '.', '');
        $currency = $data['currency'] ?? '';
        $updated = $data['updated'] ?? '';
        $status = $data['status'] ?? '';
        $created = $data['created'] ?? '';

        return "x_id{$id}x_amount{$amount}x_currency{$currency}x_updated{$updated}x_status{$status}x_created{$created}";
    }

    /**
     * Get decimal places for currency (ISO standard)
     */
    private function getDecimalPlaces(string $currency): int
    {
        $threePlaceCurrencies = ['BHD', 'KWD', 'OMR'];
        return in_array(strtoupper($currency), $threePlaceCurrencies) ? 3 : 2;
    }

    /**
     * Handle charge webhook events
     */
    private function handleChargeWebhook(array $data): string
    {
        $chargeId = $data['id'];
        $status = $data['status'];
        $amount = $data['amount'];
        $currency = $data['currency'];

        Log::info('Processing charge webhook', [
            'charge_id' => $chargeId,
            'status' => $status,
            'amount' => $amount,
            'currency' => $currency
        ]);

        // In a real application, you would:
        // 1. Update charge status in your database
        // 2. Send confirmation emails  
        // 3. Update order status
        // 4. Trigger business logic based on status
        
        $notes = "Charge webhook processed for {$chargeId}. ";
        
        switch ($status) {
            case 'CAPTURED':
                Log::info('Charge captured successfully', ['charge_id' => $chargeId]);
                $notes .= "Payment captured successfully.";
                // Handle successful payment
                break;
            case 'FAILED':
                Log::info('Charge failed', ['charge_id' => $chargeId]);
                $notes .= "Payment failed.";
                // Handle failed payment
                break;
            default:
                Log::info('Charge status update', ['charge_id' => $chargeId, 'status' => $status]);
                $notes .= "Status updated to {$status}.";
        }
        
        return $notes;
    }

    /**
     * Handle authorize webhook events
     */
    private function handleAuthorizeWebhook(array $data): string
    {
        $authId = $data['id'];
        $status = $data['status'];
        
        Log::info('Processing authorize webhook', [
            'auth_id' => $authId,
            'status' => $status
        ]);

        return "Authorization webhook processed for {$authId} with status {$status}.";
    }

    /**
     * Handle invoice webhook events
     */
    private function handleInvoiceWebhook(array $data): string
    {
        $invoiceId = $data['id'];
        $status = $data['status'];
        
        Log::info('Processing invoice webhook', [
            'invoice_id' => $invoiceId,
            'status' => $status
        ]);

        return "Invoice webhook processed for {$invoiceId} with status {$status}.";
    }

    /**
     * Detect card brand from card number
     */
    private function detectCardBrand($number)
    {
        $number = str_replace(' ', '', $number);
        
        if (preg_match('/^4/', $number)) {
            return 'VISA';
        } elseif (preg_match('/^5[1-5]/', $number)) {
            return 'MASTERCARD';
        } elseif (preg_match('/^3[47]/', $number)) {
            return 'AMEX';
        } elseif (preg_match('/^6(?:011|5)/', $number)) {
            return 'DISCOVER';
        }
        
        return 'UNKNOWN';
    }

    /**
     * Create a token from Apple Pay token using Tap Payments API
     *
     * Implementation based on Apple Pay integration documentation:
     * - Converts Apple Pay payment tokens to Tap tokens
     * - Requires Apple Pay token_data with encrypted payment information
     * - Returns standard Tap token for use in charges/authorize calls
     *
     * Apple Pay token contains:
     * - Encrypted payment data from Apple's secure element
     * - Transaction header with public keys and transaction ID
     * - Digital signature for verification
     * - Version information for compatibility
     */
    public function createApplePayToken(Request $request)
    {
        Log::info('Apple Pay token conversion request received', [
            'client_ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'token_data' => $request->input('token_data'), // This will log the whole token_data object
            'request_size' => strlen(json_encode($request->all()))
        ]);

        // Corrected Validation
        $validatedData = $request->validate([
            'token_data' => 'required|array',
            'token_data.jsonAppleToken' => 'required|array',
            'token_data.jsonAppleToken.version' => 'required|string',
            'token_data.jsonAppleToken.data' => 'required|string',
            'token_data.jsonAppleToken.header' => 'required|array',
            'token_data.jsonAppleToken.header.transactionId' => 'required|string',
            'token_data.jsonAppleToken.header.publicKeyHash' => 'required|string',
            'token_data.jsonAppleToken.header.ephemeralPublicKey' => 'required|string',
            'token_data.jsonAppleToken.signature' => 'required|string',
        ]);

        // Access the nested jsonAppleToken
        $applePayToken = $validatedData['token_data']['jsonAppleToken'];

        try {
            Log::info('Validated Apple Pay token structure', [
                'data_length' => strlen($applePayToken['data']),
                'version' => $applePayToken['version'],
                'transaction_id' => $applePayToken['header']['transactionId'],
                'has_signature' => !empty($applePayToken['signature'])
            ]);

            // Prepare the request payload as per Tap API Apple Pay documentation
            // using the correctly accessed nested data
            $payload = [
                'type' => 'applepay',
                'token_data' => [
                    'data' => $applePayToken['data'],
                    'header' => [
                        'transactionId' => $applePayToken['header']['transactionId'],
                        'publicKeyHash' => $applePayToken['header']['publicKeyHash'],
                        'ephemeralPublicKey' => $applePayToken['header']['ephemeralPublicKey'],
                    ],
                    'signature' => $applePayToken['signature'],
                    'version' => $applePayToken['version'],
                ],
                'client_ip' => $request->ip()
            ];

            Log::info('Prepared Apple Pay token payload for Tap API', [
                'payload_structure' => [
                    'type' => $payload['type'],
                    'client_ip' => $payload['client_ip'],
                    'token_data_keys' => array_keys($payload['token_data']),
                    'header_keys' => array_keys($payload['token_data']['header']),
                ]
            ]);

            Log::info("Full payload:", $payload);

            // Make actual API call to Tap Payments
            Log::info('Making API call to Tap Payments for Apple Pay token conversion');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->SECRET_KEY, // Ensure $this->SECRET_KEY is defined
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post(self::TAP_API_BASE . '/v2/tokens', $payload); // Ensure self::TAP_API_BASE is defined

            Log::info('Tap API response received', [
                'status_code' => $response->status(),
                'response_size' => strlen($response->body()),
                'has_response_body' => !empty($response->body())
            ]);

            if ($response->successful()) {
                $tokenData = $response->json();

                Log::info('Apple Pay token conversion successful', [
                    'tap_token_id' => $tokenData['id'] ?? 'unknown',
                    'token_created' => $tokenData['created'] ?? 'unknown',
                    'token_used' => $tokenData['used'] ?? false,
                    'card_info' => [
                        'first_six' => $tokenData['card']['first_six'] ?? 'unknown',
                        'last_four' => $tokenData['card']['last_four'] ?? 'unknown',
                        'brand' => $tokenData['card']['brand'] ?? 'unknown',
                        'scheme' => $tokenData['card']['scheme'] ?? 'unknown',
                    ]
                ]);

                return response()->json([
                    'success' => true,
                    'token' => $tokenData,
                    'debug' => [
                        'conversion_type' => 'apple_pay_to_tap_token',
                        'apple_pay_version' => $payload['token_data']['version'],
                        'note' => 'Real API response from Tap Payments Apple Pay conversion'
                    ]
                ]);
            } else {
                $errorData = $response->json();
                $errorMessage = 'Apple Pay token conversion failed';
                if (isset($errorData['errors']) && is_array($errorData['errors']) && !empty($errorData['errors'])) {
                    $firstError = reset($errorData['errors']);
                    $errorMessage = $firstError['description'] ?? ($firstError['message'] ?? $errorMessage);
                } elseif (isset($errorData['error']['message'])) {
                    $errorMessage = $errorData['error']['message'];
                }


                Log::error('Apple Pay token conversion failed', [
                    'status_code' => $response->status(),
                    'error_response' => $errorData,
                    'error_type' => $errorData['error']['type'] ?? ($errorData['errors'][0]['type'] ?? 'unknown'),
                    'error_code' => $errorData['error']['code'] ?? ($errorData['errors'][0]['code'] ?? 'unknown'),
                    'error_message' => $errorMessage
                ]);

                return response()->json([
                    'success' => false,
                    'error' => $errorMessage,
                    'debug' => [
                        'status' => $response->status(),
                        'response' => $errorData,
                        'conversion_type' => 'apple_pay_to_tap_token'
                    ]
                ], $response->status());
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Apple Pay token validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Invalid Apple Pay token data provided.',
                'details' => $e->errors(),
                'debug' => [
                    'conversion_type' => 'apple_pay_to_tap_token',
                    'exception_class' => get_class($e)
                ]
            ], 422);
        } catch (\Exception $e) {
            Log::error('Apple Pay token conversion exception', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'request_data' => $request->all() // Log all request data for debugging exceptions
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Internal server error during Apple Pay token conversion: ' . $e->getMessage(),
                'debug' => [
                    'conversion_type' => 'apple_pay_to_tap_token',
                    'exception_class' => get_class($e)
                ]
            ], 500);
        }
    }
} 