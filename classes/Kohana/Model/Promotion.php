<?php

class Kohana_Model_Promotion extends Jam_Model implements Sellable {

	/**
	 * @codeCoverageIgnore
	 */
	public static function initialize(Jam_Meta $meta)
	{
		$meta
			->table('promotions')
			->associations(array(
				'purchase_items' => Jam::association('hasmany', array(
					'as' => 'reference',
				))
			))
			->fields(array(
				'id' => Jam::field('primary'),
				'name' => Jam::field('string'),
				'description' => Jam::field('text'),
				'model' => Jam::field('polymorphic'),
				'created_at' => Jam::field('timestamp', array(
					'format' => 'Y-m-d H:i:s',
					'auto_now_create' => TRUE,
				)),
				'expires_at' => Jam::field('timestamp', array(
					'format' => 'Y-m-d H:i:s',
				)),
			))
			->validator('name', array('present' => TRUE))
			->validator('currency', array('currency' => TRUE));
	}

	public function price_for_purchase_item(Model_Purchase_Item $purchase_item)
	{
		throw new Kohana_Exception('Not a valid promotion');
	}

	public function applies_to(Model_Store_Purchase $purchase)
	{
		throw new Kohana_Exception('Not a valid promotion');
	}

	public function build_purchase_item()
	{
		return $this->purchase_items->build(array(
			'type' => 'promotion',
			'is_payable' => TRUE,
		));
	}

	public function currency()
	{
		return $this->currency;
	}

	public function update_store_purchase(Model_Store_Purchase $store_purchase)
	{
		$promo_item = $this->build_purchase_item();
		$item_offset = $store_purchase->find_same_item($promo_item);

		if ($this->applies_to($store_purchase)) 
		{
			$store_purchase->items[$item_offset] = $promo_item;
		}
		elseif ($item_offset !== NULL)
		{
			unset($store_purchase->items[$item_offset]);
		}
	}
}
