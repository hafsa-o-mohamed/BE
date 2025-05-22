Title: Payment Agreement and Contracts

URL Source: http://developers.tap.company/docs/payment-agreement

Markdown Content:
Tap Payments provides a streamlined process for transactions using saved cards, allowing merchants to store customer cards under specific payment agreements. These agreements permit the storage of cards with 3D Secure enabled and later, to charge the customer without requiring 3D Secure, adhering to the agreed-upon terms. This guide offers a comprehensive overview of this process, detailing the types of agreements available and the implementation plan for this feature.

A payment agreement is a mutual arrangement between the merchant and the customer that authorizes the merchant to carry out transactions on the customer's card. This arrangement facilitates more flexible transactions with saved cards while maintaining security measures that protect against unauthorized use. The use of a saved card necessitates clear consent from the customer, defined within the payment agreement terms, thus authorizing the merchant to make charges per the agreed terms using the saved card.

There are five contract types available for establishing customer payment agreements:

This agreement allows for flexibility and openness, enabling merchants to utilize the saved card for any purpose as defined by the merchant.

**Example:** A customer agrees to save their card details with a merchant to facilitate future purchases without completing the entire payment process. As per their agreement, the merchant can charge the customer for various goods or services.

This agreement enables the merchant to save the card exclusively for use within a specific subscription. The charged amount remains fixed and tied to the subscription.

**Example:** A customer subscribes to a monthly plan for a streaming service. Under a subscription contract, the merchant saves the customer's card details and automatically charges the fixed subscription fee to the customer's card each month.

This agreement applies when customers choose to make payments in installments. The charged amount remains fixed and corresponds to the installment plan.

**Example:** A customer purchases a high-value item and opts to pay in installments. The merchant saves the customer's card details under an installment contract and charges the agreed-upon installment amount at regular intervals until the full payment is completed.

This agreement links payments to the completion of specific milestones or services. The charged amount varies and depends on the achieved milestones.

**Example:** A customer hires a contractor for a home renovation project. The merchant saves the customer's card details under a milestone contract and charges the customer after the completion of each defined milestone.

The order contract represents a unique type of payment agreement, allowing the merchant to charge the customer after delivering goods or services. Instead of the usual authorize-capture process, this agreement enables the merchant to charge the customer at any time based on dispatched items. The charged amount varies and aligns with the dispatched items.

**Example:** A customer places an order for various items from an online store. The merchant saves the customer's card details under an order contract. As the merchant ships the items, they charge the customer's card for the corresponding amount of the dispatched items.

Updated over 1 year ago

* * *

Did this page help you?

*   [Table of Contents](http://developers.tap.company/docs/payment-agreement#)
*       *   [Payment Agreement](http://developers.tap.company/docs/payment-agreement#payment-agreement)
        *   [Card Contract - Payment Agreement](http://developers.tap.company/docs/payment-agreement#card-contract---payment-agreement)
        *   [Subscription Contract - Payment Agreement](http://developers.tap.company/docs/payment-agreement#subscription-contract---payment-agreement)
        *   [Installment Contract - Payment Agreement](http://developers.tap.company/docs/payment-agreement#installment-contract---payment-agreement)
        *   [Milestone Contract - Payment Agreement](http://developers.tap.company/docs/payment-agreement#milestone-contract---payment-agreement)
        *   [Order Contract - Payment Agreement](http://developers.tap.company/docs/payment-agreement#order-contract---payment-agreement)
