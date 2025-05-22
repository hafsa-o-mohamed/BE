# Tap Payments Implementation

## Overview
This document outlines the implementation of Tap Payments integration with Apple Pay support for the application.

## Database Structure

### Table: `tap_payment_transactions`
Stores all payment-related data with these key features:
- Stores both request and response data for complete transaction history
- Tracks payment status, amounts, and card details
- Includes idempotency keys for replay protection
- Maintains backup data for system resilience

## Service-Oriented Architecture

### 1. Base Service (`TapPaymentService`)
- Location: `/app/Services/TapPayment/TapPaymentService.php`
- Functionality:
  - Handles common API communication
  - Provides logging and backup functionality
  - Manages database interactions

### 2. Apple Pay Service (`ApplePayService`)
- Location: `/app/Services/TapPayment/ApplePayService.php`
- Functionality:
  - Processes Apple Pay tokens as per Tap documentation
  - Validates token data structure
  - Handles charge creation with tokens

### 3. Payment Agreement Service (`PaymentAgreementService`)
- Location: `/app/Services/TapPayment/PaymentAgreementService.php`
- Functionality:
  - Manages recurring payment agreements
  - Supports different contract types (UNSCHEDULED, SUBSCRIPTION, etc.)
  - Handles merchant-initiated transactions

### 4. Webhook Service (`WebhookService`)
- Location: `/app/Services/TapPayment/WebhookService.php`
- Functionality:
  - Processes incoming webhooks from Tap
  - Updates transaction statuses
  - Handles different event types (charge.succeeded, charge.failed, etc.)

## Controller Implementation

### `TapPaymentController`
- Location: `/app/Http/Controllers/Api/TapPaymentController.php`
- Endpoints:
  - Processing Apple Pay tokens
  - Creating charges
  - Managing payment agreements
  - Handling webhooks
  - Retrieving transaction history

## API Endpoints

All endpoints are available under `/api/tap-payments/`:

| Endpoint | Method | Description | Authentication |
|----------|--------|-------------|----------------|
| `/webhook` | POST | Handles Tap Payments webhooks | Public |
| `/charges` | POST | Creates a new charge | Required |
| `/charges/{chargeId}` | GET | Retrieves charge details | Required |
| `/apple-pay/token` | POST | Processes Apple Pay tokens | Required |
| `/agreements` | POST | Creates payment agreements | Required |
| `/merchant-charges` | POST | Creates merchant-initiated charges | Required |
| `/transactions` | GET | Retrieves transaction history | Required |

## Security Features

The implementation includes:
- Comprehensive validation for all inputs
- Idempotency keys to prevent duplicate transactions
- Backup of all transaction data to text files
- Detailed logging of all operations
- Error handling with appropriate responses

## Apple Pay Integration

The Apple Pay integration follows the Tap Payments documentation:
- Accepts Apple Pay tokens from the client
- Processes tokens to create Tap tokens
- Uses tokens to create charges
- Supports recurring payments through payment agreements

## References
- [Tap Payments Documentation](https://developers.tap.company/docs)
- [Apple Pay Token Documentation](https://developers.tap.company/docs/apple-pay-token)
- [Payment Agreement Documentation](https://developers.tap.company/docs/payment-agreement)
- [Merchant Initiated Transaction Documentation](https://developers.tap.company/docs/merchant-initiated-transaction)
