# Promotions Module

The promotions module gives the ability to attach promotions to specific purchase (depends on the purchases kohana module)

It has support both for attaching promo codes and promotions to a purchase

## Promotions

Each promotion is defined by the following fields:
- __name__ - the name of the promotion
_ __description__ - short description explaining the promotion
- __type__ - the type of the promotion e.g free_shipping, discount. __Must__ be underscored word in order the callbacks to work
- __requirement__ - requirement for the purchase so the promotion is valid
- __value__ - percent or absolute value of the promotion. Depends from the __type__ field and your own implementation
- __currency__ - optional
- __requires_promo_code__ - specify whether the promotion needs an attached promo code or not
- __created_at__ - creation date
- __expires_at__ - date of expiry of the promotion


### Adding a custom promotion

By default promotions module comes with 2 promotion types:
- promotion for orders above certain purchase price (min_purchase_price)
- promotion with promo code attached to the purchase (discount)

In order to add your own promotions, one must implement 2 specific methods in the Promotion model:

- applies_to_{{promotion_type}} - the method is called when $purchase->update_promotions() is called. Must return boolean.
- price_{{promotion_type}} - the method calculates the price of the specified promotion


## License

Copyright (c) 2012-2013, OpenBuildings Ltd. Developed by Yasen Yanev as part of [clippings.com](http://clippings.com)

Under BSD-3-Clause license, read LICENSE file.

