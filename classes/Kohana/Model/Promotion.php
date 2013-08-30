<?php

use OpenBuildings\Monetary\Monetary;

class Kohana_Model_Promotion extends Jam_Model implements Sellable {

	const TYPE_DISCOUNT = 'discount';
	const TYPE_MINIMUM_PURCHASE_PRICE = 'min-purchase-price';

	public static $allowed_types = array(
		self::TYPE_DISCOUNT,
		self::TYPE_MINIMUM_PURCHASE_PRICE,
	);

	public static function initialize(Jam_Meta $meta)
	{
		$meta
			->associations(array(
				'promo_codes' => Jam::association('hasmany', array(
					'inverse_of' => 'promotion'
				))		
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

	public function currency(Model_Purchase_Item $purchase_item)
	{
		return $this->currency;
	}

	public function price(Model_Purchase_Item $purchase_item)
	{
		$callback = 'price_'.Inflector::underscore($this->type);
			
		if (method_exists($this, $callback))
			return call_user_func(array($this, $callback), $purchase_item);
		else
			return -$this->value;
	}

	public function price_min_purchase_price(Model_Purchase_Item $purchase_item)
	{
		return -$purchase_item->store_purchase->total_price('product') * $this->value / 100;
	}

	public function price_discount(Model_Purchase_Item $purchase_item)
	{
		return -$this->value;
	}

	public function applies_to(Model_Purchase_Item $purchase_item)
	{
		$callback = 'applies_to_'.Inflector::underscore($this->type);
	
		if (method_exists($this, $callback))
			return call_user_func(array($this, $callback), $purchase_item);
		else
			return FALSE;
	}

	public function applies_to_min_purchase_price(Model_Purchase_Item $purchase_item)
	{
		return ($purchase_item->purchase_insist()->total_price('product') >= (float) $this->requirement AND 
						( ! $this->requires_promo_code OR 
						 ($this->requires_promo_code AND $promo_code = $purchase_item->purchase_insist()->promo_code AND 
						 	$promo_code->promotion->id() == $this->id())));
	}

	public function applies_to_discount(Model_Purchase_Item $purchase_item)
	{
		return (($this->requires_promo_code AND $promo_code = $purchase_item->purchase_insist()->promo_code 
						 AND $promo_code->promotion->id() == $this->id()) OR ( ! $this->requires_promo_code));
	}	
}
