Title: Liability Shift: Customer vs Merchant

URL Source: http://developers.tap.company/docs/liability-shift-customer-vs-merchant

Markdown Content:
Liability Shift: Customer vs Merchant

===============

[Jump to Content](http://developers.tap.company/docs/liability-shift-customer-vs-merchant#content)

[![Image 1: Tap API Docs 1.0](https://files.readme.io/cc73b8e-tap-logo-white.svg)](http://developers.tap.company/)[Home](http://developers.tap.company/)

[Home](http://developers.tap.company/)[Guides](http://developers.tap.company/docs)[API Reference](http://developers.tap.company/reference)

* * *

[Home](http://developers.tap.company/)[Log In](http://developers.tap.company/login?redirect_uri=/docs/liability-shift-customer-vs-merchant)[![Image 2: Tap API Docs 1.0](https://files.readme.io/cc73b8e-tap-logo-white.svg)](http://developers.tap.company/)

Guides

[Log In](http://developers.tap.company/login?redirect_uri=/docs/liability-shift-customer-vs-merchant)

[Home](http://developers.tap.company/)[Guides](http://developers.tap.company/docs)[API Reference](http://developers.tap.company/reference)Liability Shift: Customer vs Merchant

Search

CTRL-K

All

Guides

Reference

###### Start typing to search…

Acceptance
----------

*   [Saved Cards](http://developers.tap.company/docs/saved-cards)
    *   [Payment Agreement and Contracts](http://developers.tap.company/docs/payment-agreement)
    *   [Creating Payment Agreement](http://developers.tap.company/docs/creating-payment-agreement)
    *   [Merchant Initiated Transaction](http://developers.tap.company/docs/merchant-initiated-transaction)
    *   [Liability Shift: Customer vs Merchant](http://developers.tap.company/docs/liability-shift-customer-vs-merchant)

*   [Recurring Payments](http://developers.tap.company/docs/recurring-payments)

SDK
---

*   [Web Card SDK V1](http://developers.tap.company/docs/card-sdk-web-v1)
*   [Web Card SDK V2](http://developers.tap.company/docs/card-sdk-web-v2)

Payment methods
---------------

*   [Overview of Payment Methods](http://developers.tap.company/docs/payment-methods)
*   [Samsung Pay](http://developers.tap.company/docs/samsung-pay-token)
*   [Card Payments](http://developers.tap.company/docs/card-payments)
    *   [Cards](http://developers.tap.company/docs/cards)
    *   [Google Pay](http://developers.tap.company/docs/google-pay)
    *   [OmanNet](http://developers.tap.company/docs/omannet)
    *   [Benefit](http://developers.tap.company/docs/benefit)

*   [Benefit Pay](http://developers.tap.company/docs/benefitpay-sdk)
    *   [Web](http://developers.tap.company/docs/benefitpay-web-sdk)
    *   [iOS](http://developers.tap.company/docs/benefitpay-sdk-ios)
    *   [Android](http://developers.tap.company/docs/benefitpay-sdk-android)
    *   [React-Native](http://developers.tap.company/docs/benefitpay-sdk-reactnative)
    *   [Flutter](http://developers.tap.company/docs/benefitpay-sdk-flutter)

*   [Apple Pay](http://developers.tap.company/docs/apple-pay)
    *   [Apple Pay Token](http://developers.tap.company/docs/apple-pay-token)
    *   [Apple Pay Web SDK](http://developers.tap.company/docs/apple-pay-web-sdk)
    *   [Apple Pay Recurring](http://developers.tap.company/docs/apple-pay-recurring)

*   [Cash Wallet](http://developers.tap.company/docs/fawry)
    *   [Fawry](http://developers.tap.company/docs/fawry)

*   [KNET](http://developers.tap.company/docs/knet)
    *   [KFAST](http://developers.tap.company/docs/kfast)

*   [Mada](http://developers.tap.company/docs/mada)
    *   [Mada Recurring](http://developers.tap.company/docs/mada-recurring)

*   [NAPS/QPay](http://developers.tap.company/docs/qpay)
*   [STC Pay](http://developers.tap.company/docs/stcpay)
*   [BNPL](http://developers.tap.company/docs/tabby)
    *   [Tabby](http://developers.tap.company/docs/tabby)
    *   [Deema](http://developers.tap.company/docs/deema)

Plugins
-------

*   [Woocommerce](http://developers.tap.company/docs/woocommerce)
*   [Magento](http://developers.tap.company/docs/magento)

After Payment
-------------

*   [Webhook](http://developers.tap.company/docs/webhook)
*   [Redirect](http://developers.tap.company/docs/redirect)

Marketplace
-----------

*   [Overview](http://developers.tap.company/docs/marketplace-overview)
*   [Getting Started](http://developers.tap.company/docs/marketplace-getting-started)
*   [Onboarding Businesses](http://developers.tap.company/docs/marketplace-onboarding-businesses)
*   [Split Payments](http://developers.tap.company/docs/marketplace-split-payments)

References
----------

*   [User Access Permissions](http://developers.tap.company/docs/user-access-permissions)

Integrations Flow
-----------------

*   [Card Payments](http://developers.tap.company/docs/card-payments-integration-flow)
*   [Redirect Payments](http://developers.tap.company/docs/redirect-payments-integration-flow)
*   [Device Payments](http://developers.tap.company/docs/device-payments-integration-flow)
*   [Authorize and Capture](http://developers.tap.company/docs/authorize-and-capture)
*   [Recommendations & Best Practices](http://developers.tap.company/docs/recommendations-best-practices)
*   [Encrypted Card Flow (PCI)](http://developers.tap.company/docs/encrypted-card-flow-pci)
*   [Save Card Use Case Scenarios](http://developers.tap.company/docs/save-card-use-case-scenarios)
*   [Idempotency in Payment Processing](http://developers.tap.company/docs/idempotency)

Platform
--------

*   [Platforms Setups](http://developers.tap.company/docs/platforms-setup)
*   [Platforms Integration Concepts](http://developers.tap.company/docs/platforms-integration-concepts)
*   [Creating a Lead](http://developers.tap.company/docs/creating-a-lead)
*   [Creating a Merchant Account](http://developers.tap.company/docs/creating-a-merchant-account)
*   [Creating a Transaction](http://developers.tap.company/docs/creating-a-transaction)

Reports
-------

*   [Reports Download Concepts](http://developers.tap.company/docs/reports-concepts)
*   [Payout Report](http://developers.tap.company/docs/payout-report)
*   [Reports for Commerce Platforms](http://developers.tap.company/docs/commerce-platform-reports)

Liability Shift: Customer vs Merchant
=====================================

Understanding who bears the responsibility for fraudulent activity or unauthorized charges — known as the liability shift — is vital for customers and merchants. This section examines two scenarios: one where a customer initiates a transaction with 3D Secure (3DS) and subsequent transactions without it, and another involving non-3DS transactions initiated by either party.

Liability shift to the customer:

[](http://developers.tap.company/docs/liability-shift-customer-vs-merchant#liability-shift-to-the-customer)
=============================================================================================================================================

In scenarios where the customer initiates a 3DS transaction, they assume liability for any fraudulent or unauthorized charges. Completing the 3DS verification typically reduces their risk of fraud. However, should the merchant process subsequent non-3DS transactions under a payment agreement referencing the customer's initial 3DS transaction, the customer might retain liability.

**Example:**A customer makes a verified online purchase via 3DS, reducing their fraud liability. Yet, if the merchant processes later non-3DS transactions under the same agreement and fraud occurs, the customer could be held accountable.

Liability shift to the merchant

[](http://developers.tap.company/docs/liability-shift-customer-vs-merchant#liability-shift-to-the-merchant)
============================================================================================================================================

Conversely, when a non-3DS transaction is customer-initiated, such as a "card not present" transaction, the merchant often assumes liability for any unauthorized or fraudulent charges. If a non-3DS transaction is later contested as fraudulent, the merchant typically bears the financial loss or chargebacks.

**Example:**A customer completes an online purchase without 3DS verification. If they dispute subsequent charges as unauthorized, liability usually shifts to the merchant, who must handle the financial repercussions and any chargebacks.

It's crucial for both parties to understand the implications of the liability shift in payment transactions. Merchants and customers must follow industry best practices and maintain robust security to minimize fraud and uphold a secure payment ecosystem, recognizing their obligations and the associated risks.

Updated over 1 year ago

* * *

[Merchant Initiated Transaction](http://developers.tap.company/docs/merchant-initiated-transaction)[Recurring Payments](http://developers.tap.company/docs/recurring-payments)

Did this page help you?

Yes

No

*   [Table of Contents](http://developers.tap.company/docs/liability-shift-customer-vs-merchant#)
*       *   [Liability shift to the customer:](http://developers.tap.company/docs/liability-shift-customer-vs-merchant#liability-shift-to-the-customer)
    *   [Liability shift to the merchant](http://developers.tap.company/docs/liability-shift-customer-vs-merchant#liability-shift-to-the-merchant)
