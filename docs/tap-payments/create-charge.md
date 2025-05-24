```markdown
# Create a Charge

This endpoint initiates a charge request to charge a credit card or any other payment source.

If your API key is in test mode, the provided payment source (e.g., [test card](https://developers.tap.company/reference/testing-cards)) will not be charged, although all other operations will be treated as if in live mode (Tap assumes that the charge would have been completed successfully.)

## API Request

`POST /`

**Authentication:** Required

### Body Parameters

| Name              | Type      | Default                      | Description                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          | Required |
| :---------------- | :-------- | :--------------------------- | :----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | :------- |
| `amount`          | `float`   | `1`                          | The amount to be collected by this payment, in ISO standard decimal places. A positive decimal representing how much to charge in the currency unit (e.g: 100 to charge $100 and 100.5 to charge $100.50). The minimum amount is $0.100 for any charge currency.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           | True     |
| `currency`        | `string`  | `KWD`                        | Three-letter ISO currency code, in uppercase. Must be a supported currency.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          | True     |
| `customer_initiated` | `boolean` | `true`                       | This parameter determines whether the charge was initiated by customer or not                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        | False    |
| `threeDSecure`    | `boolean` | `true`                       | The 3D Secure request status for a particular charge. Values can be either ``true`` or ``false``.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    | False    |
| `save_card`       | `boolean` | `false`                      | Payer can save credit cards for future purpose but a customer phone number is required to save the card. Values can be either be True or False. In order to use this feature, save card feature needs to be activated on the Merchant Account.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            | False    |
| `payment_agreement` | `object`  |                              | Serves as a unique identifier for referencing and managing the payment agreement                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     | False    |
| `description`     | `string`  | `Test Description`           | An arbitrary string which you can attach to a ``Charge`` request with more details, if needed.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       | False    |
| `order`           | `object`  |                              | The object represents a specific order information such as order ID of the related transaction.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      | False    |
| `metadata`        | `object`  |                              | The set of key-value pairs that you can attach to an object. This can be useful for storing additional information about the object in a structured format.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            | False    |
| `receipt`         | `object`  |                              | Note: this feature is no longer supported                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            | False    |
| `reference`       | `object`  |                              | The reference numbers related to the charge.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         | False    |
| `customer`        | `object`  | `"first_name": "test", ...`  | The details about the customer who will be performing the transaction.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               | True     |
| `merchant`        | `object`  |                              | The ID of the Merchant Account. Available on the Tap Dashboard (goSell > API Credentials > Merchant ID)                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              | False    |
| `source`          | `object`  |                              | The details about the payment method at the time of the transaction. Possible values: for local payment methods, use the respective source_id. Example: For KNET, use ``src_kw.knet``; for capturing a token, use the ``token_id``; to authorize an existing authorized transaction use the ``authorize_id``; to display all payment methods in a Tap hosted page, use ``src_all``; to display only the card payment methods in a Tap hosted page, use ``src_card``; for capturing from saved card for customer, create a token by using [Create a Token API](https://developers.tap.company/reference/create-a-token-from-saved-card) (make sure to pass ``customer_id`` in the customer object); . Refer to Payment Methods guide for more information. | True     |
| `post`            | `object`  |                              | The Webhook URL. After payment is completed, Tap will POST the response payload as a raw data to this URL.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           | False    |
| `redirect`        | `object`  |                              | After payment is completed, payer will be redirected to this URL (KNET, mada and 3D secure charge requests requires Redirect URL)                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        | True     |
| `payment_provider` | `object`  |                              | Information about the payment provider associated with this charge                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   | False    |
| `platform`        | `object`  |                              | The Platform parameter identifies the platform under which the merchant is operating, linking the transaction to the appropriate platform.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               | False    |

### Examples

**Charge Request with Payment Agreement**

```json
{
  "amount": 1,
  "currency": "KWD",
  "customer_initiated": true,
  "threeDSecure": true,
  "save_card": false,
  "payment_agreement": {
    "id": "payment_agreement_TS07A4620230032t4K21406294"
  },
  "description": "Test Description",
  "metadata": {
    "udf1": "Metadata 1"
  },
  "reference": {
    "transaction": "txn_01",
    "order": "ord_01"
  },
  "receipt": {
    "email": true,
    "sms": true
  },
  "customer": {
    "id": "cus_TS01A4620230032p4KP1406279"
  },
  "source": {
    "id": "tok_2uKe58232153ZmxV138r5c637"
  },
  "post": {
    "url": "http://your_website.com/post_url"
  },
  "redirect": {
    "url": "http://your_website.com/redirect_url"
  }
}
```

### Responses

#### 200 OK (Charge Response with Payment Agreement)

```json
{
  "id": "chg_TS02A2720230055g4JO1406864",
  "object": "charge",
  "live_mode": false,
  "customer_initiated": true,
  "api_version": "V2",
  "method": "GET",
  "status": "CAPTURED",
  "amount": 1.000,
  "currency": "KWD",
  "threeDSecure": true,
  "card_threeDSecure": false,
  "save_card": false,
  "merchant_id": "",
  "product": "GOSELL",
  "description": "Test Description",
  "metadata": {
    "udf1": "Metadata 1"
  },
  "transaction": {
    "authorization_id": "100204",
    "timezone": "UTC+03:00",
    "created": "1686704127943",
    "expiry": {
      "period": 30,
      "type": "MINUTE"
    },
    "asynchronous": false,
    "amount": 1.000,
    "currency": "KWD"
  },
  "reference": {
    "track": "tck_TS02A2920230055e5R41406896",
    "payment": "2914230055068969225",
    "gateway": "123456789012345",
    "acquirer": "316421100204",
    "transaction": "txn_01",
    "order": "ord_01"
  },
  "response": {
    "code": "000",
    "message": "Captured"
  },
  "security": {
    "threeDSecure": {
      "id": "3ds_TS07A2720230055l5Q21406943",
      "status": "Y"
    }
  },
  "acquirer": {
    "response": {
      "code": "00",
      "message": "Approved"
    }
  },
  "gateway": {
    "response": {
      "code": "0",
      "message": "Transaction Approved"
    }
  },
  "card": {
    "object": "card",
    "first_six": "424242",
    "scheme": "VISA",
    "brand": "VISA",
    "last_four": "4242"
  },
  "receipt": {
    "id": "203014230055062725",
    "email": true,
    "sms": true
  },
  "customer": {
    "id": "cus_TS01A4620230032p4KP1406279",
    "first_name": "test",
    "middle_name": "test",
    "last_name": "test",
    "email": "test@test.com",
    "phone": {
      "country_code": "965",
      "number": "51234567"
    }
  },
  "merchant": {
    "country": "KW",
    "currency": "KWD",
    "id": "599424"
  },
  "source": {
    "object": "token",
    "type": "CARD_NOT_PRESENT",
    "payment_type": "CREDIT",
    "payment_method": "VISA",
    "channel": "INTERNET",
    "id": "tok_2uKe58232153ZmxV138r5c637"
  },
  "redirect": {
    "status": "SUCCESS",
    "url": "http://your_website.com/redirect_url"
  },
  "post": {
    "status": "ERROR",
    "url": "http://your_website.com/post_url"
  },
  "activities": [
    {
      "id": "activity_TS07A3020230055j5O91406255",
      "object": "activity",
      "created": 1686704127943,
      "status": "INITIATED",
      "currency": "KWD",
      "amount": 1.000,
      "remarks": "charge - created"
    },
    {
      "id": "activity_TS05A4220230057o2L51406773",
      "object": "activity",
      "created": 1686704262773,
      "status": "CAPTURED",
      "currency": "KWD",
      "amount": 1.000,
      "remarks": "charge - captured"
    }
  ],
  "auto_reversed": false,
  "payment_agreement": {
    "id": "payment_agreement_TS07A4620230032t4K21406294",
    "total_payments_count": 1,
    "contract": {
      "id": "card_YOh6102321312TNL13qE5C584",
      "type": "UNSCHEDULED"
    },
    "variable_amount": {
      "id": "variable_amount_TS07A4620230032Ra9k1406294"
    }
  }
}
```

```json
{
  "id": "chg_TS012520220955Rr950709475",
  "object": "charge",
  "live_mode": false,
  "api_version": "V2",
  "method": "CREATE",
  "status": "INITIATED",
  "amount": 1.000,
  "currency": "KWD",
  "threeDSecure": true,
  "card_threeDSecure": false,
  "save_card": false,
  "merchant_id": "",
  "product": "GOSELL",
  "description": "Test Description",
  "metadata": {
    "udf1": "Metadata 1"
  },
  "transaction": {
    "timezone": "UTC+03:00",
    "created": "1662544525491",
    "url": "https://checkout.payments.tap.company?mode=page&token=6318405da53ea40ebd4da0c0",
    "expiry": {
      "period": 30,
      "type": "MINUTE"
    },
    "asynchronous": false,
    "amount": 1.000,
    "currency": "KWD"
  },
  "reference": {
    "transaction": "txn_01",
    "order": "ord_01"
  },
  "response": {
    "code": "100",
    "message": "Initiated"
  },
  "receipt": {
    "email": true,
    "sms": true
  },
  "customer": {
    "first_name": "test",
    "last_name": "test",
    "email": "test@test.com",
    "phone": {
      "country_code": "965",
      "number": "51234567"
    }
  },
  "merchant": {
    "id": "599424"
  },
  "source": {
    "object": "source",
    "id": "src_all"
  },
  "redirect": {
    "status": "PENDING",
    "url": "http://your_website.com/redirect_url"
  },
  "post": {
    "status": "PENDING",
    "url": "http://your_website.com/post_url"
  },
  "activities": [
    {
      "id": "activity_TS062620220955Nk440709312",
      "object": "activity",
      "created": 1662544525491,
      "status": "INITIATED",
      "currency": "KWD",
      "amount": 1.000,
      "remarks": "charge - created"
    }
  ],
  "auto_reversed": false
}
```

#### 400 Bad Request

```json
{
  "errors": [
    {
      "code": "1125",
      "description": "We were unable to process your payment. Please verify your payment method or card details and try again."
    }
  ]
}
```
```