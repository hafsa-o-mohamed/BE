# Merchant-Initiated Transactions with Tap Payments

For enabling non-3D Secure transactions in a live environment, please contact your Tap account manager to activate the necessary settings on your merchant account.

To initiate a charge under a payment agreement, certain parameters matching the agreed-upon merchant and customer terms must be included in your charge request. When these parameters are correctly configured, you can process a charge without requiring 3D Secure authentication, as it will be based on the payment agreement's prior authorization.

For illustrative purposes, a sample charge request is provided below in URL format. Use this as a model, inserting your unique details and settings, to properly formulate your charge request. Essential parameters linking the charge to the payment agreement will be outlined.

```
curl --request POST \
  --url https://api.tap.company/v2/charges \
  --header 'authorization: Bearer sk_test_xxxxxxxxxxxxxxxxxxxxxxxxx' \
  --header 'content-type: application/json' \
  --header 'lang_code: EN' \
  --data '{
	"amount": 1,
	"currency": "SAR",
	"payment_agreement": {
		"id": "payment_agreement_TS05A1920230911b2K12105431",
	},
	"customer_initiated": "false",
	"threeDSecure": true,
	"save_card": false,
	"description": "Test Description",
	"statement_descriptor": "Sample",
	"metadata": {
``
