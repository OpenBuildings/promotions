# Promotions Module

[![Build Status](https://travis-ci.org/OpenBuildings/promotions.png?branch=master)](https://travis-ci.org/OpenBuildings/promotions)
[![Coverage Status](https://coveralls.io/repos/OpenBuildings/promotions/badge.png?branch=master)](https://coveralls.io/r/OpenBuildings/promotions?branch=master)
[![Latest Stable Version](https://poser.pugx.org/openbuildings/promotions/v/stable.png)](https://packagist.org/packages/openbuildings/promotions)

This module gives the ability to define promotions with a set of requirements that add themselves to purchases, based on these rules. Each promotion can have a static or dynamic/configurable amount.

Promotions can also have ontime or multiple time uses for promo_codes

## Usage

Add a behavior to the purchase and store_purchase models:

```php
class Model_Store_Purchase extends Kohana_Model_Store_Purchase {

	public static function initialize(Jam_Meta $meta)
	{
		parent::initialize($meta);

		$meta
			->behaviors(array(
				'promotable_store_purchase' => Jam::behavior('promotable_store_purchase'),
			));
	}
}
// ...
class Model_Purchase extends Kohana_Model_Purchase {

	public static function initialize(Jam_Meta $meta)
	{
		parent::initialize($meta);

		$meta
			->behaviors(array(
				'promotable_purchase' => Jam::behavior('promotable_purchase'),
			));
	}
}
```

And you'll need to add actual promotions to the database. The promtion modle uses single table inheritence to have a different class for each promotion. Each of these has to define "applies_to" and "price_for_purchase_item" which your promotions will have to implement. There is also the ``Model_Promotion_Promocode`` class which gives the promotion the ability to use promo codes which exhaust themselves when are used.

There are 2 availbale predefined promotions:

* Model_Promotion_Promocode_Giftcard - to use it you'll need to enter requirement - the minimum price where the promotion applies, and amount - the amount (Jam_Price) to be reducted from the purchase
* Model_Promotion_Promocode_Percent - get a static reduction of some percent (amount). Amount is a value from 0 to 1. 

## promo_code_text

The ``promotable_purchase`` behavior adds a promo_code_text field to the purchase (its not in the database). When you set a promocode to this field it would try to find it, and then run "validate_purchase" of the appropriate promotion, if found. If everything checks out, the promotion associated with this promocode will be added to the purchase.

## License

Copyright (c) 2012-2013, OpenBuildings Ltd. Developed by Yasen Yanev as part of [clippings.com](http://clippings.com)

Under BSD-3-Clause license, read LICENSE file.

