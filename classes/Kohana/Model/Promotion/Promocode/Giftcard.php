<?php

class Kohana_Model_Promotion_Promocode_Giftcard extends Model_Promotion_Promocode {

	/**
	 * @codeCoverageIgnore
	 */
	public static function initialize(Jam_Meta $meta)
	{
		parent::initialize($meta);

		$meta
			->fields(array(
				'amount' => Jam::field('price'),
				'requirement' => Jam::field('price'),
			))
			->validator('amount', array('price' => array('greater_than' => 0)))
			->validator('requirement', array('price' => array('greater_than_or_equal_to' => 0)));
	}

	public function applies_to(Model_Store_Purchase $store_purchase)
	{
		if ( ! $this->matches_store_purchase_promo_code($store_purchase)) 
			return FALSE;
		
		$store_purchase_price = $store_purchase->total_price('product');

		return $store_purchase_price->is(Jam_Price::GREATER_THAN_OR_EQUAL_TO, $this->requirement);
	}

	public function price_for_purchase_item(Model_Purchase_Item $purchase_item)
	{
		return $this->amount
			->monetary($purchase_item->monetary())
			->multiply_by(-1);
	}
}
