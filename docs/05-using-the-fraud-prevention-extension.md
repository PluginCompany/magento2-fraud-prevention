# Using the Fraud Prevention Extension

Magento 2 Fraud Prevention provides a simple tool to minimize fraud and prevent suspicious orders from being placed, paid and shipped. Below are a few general guidelines on using the extension, which will be explained in details further in this user manual.

By [marking suspicious orders](./07-managing-suspicious-and-blacklisted-orders.md#suspicious-orders) in the order view, a fraud suspicion record is created including various attributes like the customer's name, contact information and address details.

The submitted [fraud suspicion records](./08-managing-fraud-suspicions.md) serve as a reference for detecting and blacklisting future fraudulent orders using custom blacklisting rules.

The [blacklisting rules](./09-creating-blacklisting-rules.md) define which customer attributes should be checked when a new order is placed and the number of 'strikes' or matching fraud suspicion records required to blacklist an order.

Each time a new order is placed, the blacklisting rule attributes are compared with the submitted fraud suspicions records. If a match is found and the permitted amount of strikes has been exceeded, the order will be automatically blacklisted and blocked for shipping.

The blacklisting rules can also be used to block customers during checkout or when updating their order or account information, and to prevent payments from being processed.

---

[Documentation index](./README.md) · [README](../README.md) · [Online version](https://plugin.company/knowledge-base/docs/magento2-fraud-prevention/user-manual/using-the-fraud-prevention-extension/)
