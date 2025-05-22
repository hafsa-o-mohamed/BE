Title: Saved Cards

URL Source: http://developers.tap.company/docs/saved-cards

Markdown Content:
Saved Cards

===============

[Jump to Content](http://developers.tap.company/docs/saved-cards#content)

[![Image 1: Tap API Docs 1.0](https://files.readme.io/cc73b8e-tap-logo-white.svg)](http://developers.tap.company/)[Home](http://developers.tap.company/)

[Home](http://developers.tap.company/)[Guides](http://developers.tap.company/docs)[API Reference](http://developers.tap.company/reference)

* * *

[Home](http://developers.tap.company/)[Log In](http://developers.tap.company/login?redirect_uri=/docs/saved-cards)[![Image 2: Tap API Docs 1.0](https://files.readme.io/cc73b8e-tap-logo-white.svg)](http://developers.tap.company/)

Guides

[Log In](http://developers.tap.company/login?redirect_uri=/docs/saved-cards)

[Home](http://developers.tap.company/)[Guides](http://developers.tap.company/docs)[API Reference](http://developers.tap.company/reference)Saved Cards

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

Saved Cards
===========

Discover how to save cards with Tap Payments to offer a seamless and quick checkout experience to your customers.

Before using the save cards feature, ensure that it is enabled on your account. If you don't have it enabled already, just contact our customer support team and they can enable it for you.

1.   To save a card, create an Authorize, Charge, or Verify Card request and set the 3D Secure and save_card flags to true in the request body.

JSON request

```json
{
  ...
  "threeDSecure": true,
  "save_card": true,
  ...
}
```

1.   The transaction response will include the card ID, which contains card information such as the brand name, first six and last four digits of the card, and more.

Transaction Response

```text
{
...
"card": {
    "id": "card_IQPXL3xxxxxxxxxxxxxxx",
    "object": "card",
    "first_six": "450875",
    "brand": "VISA",
    "last_four": "1019"
  },
...
}
```

(**Note:**You will not receive the card ID if the save cards feature is not enabled. The response through 'post_url' or 'redirect_url' contains the same details.)

1.   Once you have the card ID, you can use it for various purposes, including charging returning customers, creating subscriptions, generating fresh tokens, and more.

Updated over 1 year ago

* * *

[Payment Agreement and Contracts](http://developers.tap.company/docs/payment-agreement)

Did this page help you?

Yes

No
