```markdown
# Create a Token (Card)

This endpoint creates a single-use token that securely contains the details of a credit card.

This token can serve as a stand-in for a source in the Charges, Authorize, or Card API but it's important to note that these tokens can only be used once within a few minutes of creation. Please be aware that a PCI compliance certificate is required to use this endpoint. For more information, please contact our team. However, you can also create tokens using our Card Web SDK without needing to meet PCI compliance requirements.

## API Request

`POST /`

**Authentication:** Required

### Body Parameters

| Name        | Type     | Default          | Description                                                                 | Required |
| :---------- | :------- | :--------------- | :-------------------------------------------------------------------------- | :------- |
| `card`      | `object` |                  | Card object. Ensure that your business account has the access to use this API. | False    |
| `client_ip` | `string` | `192.168.1.20`   | The IP Address of the client.                                               | False    |

### Examples

```json
{
  "card": {
    "number": 4508750015741019,
    "exp_month": 1,
    "exp_year": 2039,
    "cvc": 100,
    "name": "test user",
    "address": {
      "country": "Kuwait",
      "line1": "Salmiya, 21",
      "city": "Kuwait city",
      "street": "Salim",
      "avenue": "Gulf"
    }
  },
  "client_ip": "192.168.1.20"
}
```

### Responses

#### 200 OK

```json
{
  "id": "tok_SpTV5823926VPgE27bU3O801",
  "created": 1682587618801,
  "object": "token",
  "live_mode": false,
  "type": "CARD",
  "used": false,
  "card": {
    "id": "card_u35O5823926okiJ27sk3D805",
    "object": "card",
    "address": {
      "country": "Kuwait",
      "city": "Kuwait city",
      "avenue": "Gulf",
      "street": "Salim",
      "line1": "Salmiya, 21"
    },
    "funding": "DEBIT",
    "fingerprint": "E1I78d8PV0UHivCrvV8dTkI%2FQJRHPow4XAU4FfmZxCQ%3D",
    "brand": "VISA",
    "scheme": "VISA",
    "name": "test user",
    "issuer": {
      "bank": "THE CO-OPERATIVE BANK PLC",
      "country": "GB",
      "id": "bnk_TS06A1120231227Kx242704090"
    },
    "exp_month": 1,
    "exp_year": 39,
    "last_four": "1019",
    "first_six": "450875"
  }
}
```
```