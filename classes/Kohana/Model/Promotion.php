<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * This is a base
 * 
 * @package    openbuildings\promotions
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @author     Yasen Yanev <yasen@openbuildings.com>
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Kohana_Model_Promotion extends Jam_Model implements Sellable {

	/**
	 * @codeCoverageIgnore
	 */
	public static function initialize(Jam_Meta $meta)
	{
		$meta
			->name_key('identifier')
			->table('promotions')
			->associations(array(
				'purchase_items' => Jam::association('hasmany', array(
					'as' => 'reference',
				))
			))
			->fields(array(
				'id' => Jam::field('primary'),
				'name' => Jam::field('string'),
				'identifier' => Jam::field('string'),
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

	/**
	 * @codeCoverageIgnore
	 */
	public function price_for_purchase_item(Model_Purchase_Item $purchase_item)
	{
		throw new Kohana_Exception('Not a valid promotion');
	}

	/**
	 * @codeCoverageIgnore
	 */
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
