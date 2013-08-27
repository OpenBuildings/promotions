<?php

use OpenBuildings\Monetary\Monetary;

class Kohana_Model_Promotion extends Jam_Model {

	const TYPE_DISCOUNT = 'discount';

	const TYPE_ELLEVISA_DISCOUNT = 'ellevisa-discount';

	const TYPE_MINIMUM_PURCHASE_PRICE = 'min-purchase-price';

	const TYPE_FREE_SHIPPING = 'free-shipping';

	public static $allowed_types = array(
		self::TYPE_DISCOUNT,
		self::TYPE_MINIMUM_PURCHASE_PRICE,
		self::TYPE_FREE_SHIPPING,
		self::TYPE_ELLEVISA_DISCOUNT,
	);

	public static $discount_types = array(
		self::TYPE_DISCOUNT,
		self::TYPE_ELLEVISA_DISCOUNT,
	);

	public static function initialize(Jam_Meta $meta)
	{
		$meta
			->associations(array(
				'promo_codes' => Jam::association('hasmany', array(
					'inverse_of' => 'promotion'
				)),
				'purchases' => Jam::association('manytomany', array(
					'inverse_of' => 'promotions'
				)),
			))
			->fields(array(
				'id' => Jam::field('primary'),
				// short name of the promotion used in invoices and checkout
				'name' => Jam::field('string'),
				// description of the promotion used in invoices and checkout
				'description' => Jam::field('text'),
				// type of the promotion: discount, free shipping, gift card etc.
				'type' => Jam::field('string'),
				// requirement for the purchase so the promotion is valid
				// depends on the type
				'requirement' => Jam::field('string'),

				// percent or absolute value of the promotion
				// depends on the type
				'value' => Jam::field('integer'),

				// Possibly used in gift cards and such
				'currency' => Jam::field('string'),

				// Indicates if the promotions can only be added from a promo code
				'requires_promo_code' => Jam::field('boolean'),

				'created_at' => Jam::field('timestamp', array(
					'format' => 'Y-m-d H:i:s',
					'auto_now_create' => TRUE
				)),
				'expires_at' => Jam::field('timestamp', array(
					'format' => 'Y-m-d H:i:s',
				)),
			))
			->validator('type', array(
				'choice' => array(
					'in' => Kohana_Model_Promotion::$allowed_types
				)
			))
			->validator('type', 'description', array('present' => TRUE))
			->validator('value', array('numeric' => TRUE))
			->validator('currency', array(
				'choice' => array(
					'in' => array_keys(Monetary::instance()->currency_templates)
				)
			));
	}

	public function discount(Model_Store_Purchase $store_purchase)
	{
		switch ($this->type) 
		{
			case self::TYPE_MINIMUM_PURCHASE_PRICE:
				return $store_purchase->total_price('product') * $this->value / 100;
				break;
			case self::TYPE_DISCOUNT:
				return $this->value;
				break;			
			default:
				return $this->value;
				break;
		}
	}

	public function applies_to(Model_Store_Purchase $store_purchase)
	{
		switch ($this->type)
		{
			case self::TYPE_MINIMUM_PURCHASE_PRICE:
				return ($store_purchase->purchase->total_price('product') >= (float) $this->requirement 
								AND ( ! $this->requires_promo_code OR ($this->requires_promo_code AND $store_purchase->purchase->promo_code)));
			break;

			case self::TYPE_DISCOUNT:
				return (($this->requires_promo_code AND $store_purchase->purchase->promo_code) OR ( ! $this->requires_promo_code));
			break;

			default:
				return FALSE;
			break;
		}
	}
}