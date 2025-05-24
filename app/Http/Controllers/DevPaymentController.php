<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DevPaymentController extends Controller
{
    /**
     * Tap Payments API Configuration
     */
    private const TAP_API_BASE = 'https://api.tap.company';
    private const SECRET_KEY = 'sk_test_XKokBfNWv6FIYuTMg5sLPjhJ';
    private const PUBLISHABLE_KEY = 'pk_test_EtHFV4BuPQokJT6jiROls87Y';

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
                'client_ip' => $request->ip()
            ];

            // Make actual API call to Tap Payments
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . self::SECRET_KEY,
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
                    'url' => route('dev.payments.charges') . '?status=completed'
                ],
                'post' => [
                    'url' => route('dev.payments.charges') . '?webhook=received'
                ]
            ];

            // Make actual API call to Tap Payments
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . self::SECRET_KEY,
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
                        'note' => 'Real API response from Tap Payments'
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
} 