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

Example:

```php

class Model_Test_Promotion extends Model_Promotion {

	const TYPE_FREE_SHIPPING = 'free_shipping';

	public function price_free_shipping(Model_Purchase_Item $purchase_item)
	{
		// perform your custom price calculations for this promotion or return a fixed promotion value
		return $this->value;
	}

  /**
   * Apply free shipping promotion for all purchases from Europe 
   * above certain price (defined in the requirement field)
   */
	public function applies_to_free_shipping(Model_Purchase_Item $purchase_item)
	{
		if ($this->type == Model_Test_Promotion::TYPE_FREE_SHIPPING)
		{
			$purchase = $purchase_item->purchase_insist();
			$country = $purchase->country;
			$currency = ($purchase->currency === 'EUR') ? 'EUR' : 'GBP';

			return ($country === 'Europe' AND $purchase->total_price('product') >= (float) $this->requirement AND $currency == $this->currency);
		}
		
		return FALSE;
	}
}

```

## Attaching promo codes

Promo codes are attached to the purchase as a string field and the corresponding promo code object (if its valid) is attached to the purchase before validation. 

Example:

```php
	$purchase->_promo_code = '2AXHG';
	if ($purchase->check())
	{
	   // we now have our promo code object attached to the purchase
	   echo $purchase->promo_code->code; // outputs '2AXHG'
	}
```


If certain promotion or promo code expires it is automatically removed when calling the $purchase->update_promotions() method. Also when a purchase covers the requirements for specific promotion, it is automatically added. Only one promotion from specific type can be added to a purchase. 

## License

Copyright (c) 2012-2013, OpenBuildings Ltd. Developed by Yasen Yanev as part of [clippings.com](http://clippings.com)

Under BSD-3-Clause license, read LICENSE file.

