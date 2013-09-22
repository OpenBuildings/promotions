<?php

class Kohana_Model_Promotion_Promocode_Percent extends Model_Promotion_Promocode {

	/**
	 * @codeCoverageIgnore
	 */
	public static function initialize(Jam_Meta $meta)
	{
		parent::initialize($meta);

		$meta
			->fields(array(
				'amount' => Jam::field('float'),
			))
			->validator('amount', array('numeric' => array('between' => array(0, 1))));
	}

	public function price_for_purchase_item(Model_Purchase_Item $purchase_item)
	{
		$store_purchase_price = $purchase_item->get_insist('store_purchase')->total_price('product');

		return $store_purchase_price
			->multiply_by($this->amount)
			->multiply_by(-1);
	}
}