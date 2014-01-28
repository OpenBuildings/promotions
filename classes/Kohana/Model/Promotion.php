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
			->unique_key(function($key){
				return is_numeric($key) ? 'id' : 'identifier';
			})
			->table('promotions')
			->associations(array(
				'purchase_items' => Jam::association('hasmany', array(
					'as' => 'reference',
					'foreign_model' => 'purchase_item_promotion',
				))
			))
			->fields(array(
				'id' => Jam::field('primary'),
				'name' => Jam::field('string'),
				'currency' => Jam::field('string'),
				'identifier' => Jam::field('string'),
				'description' => Jam::field('text'),
				'priority' => Jam::field('integer', array('default' => 1)),
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

	/**
	 * Build a purchase item for this promotion (inverseof "reference")
	 * @return Model_Purchase_Item
	 */
	public function build_purchase_item()
	{
		return $this->purchase_items->build(array('model' => 'purchase_item_promotion'));
	}

	public function currency()
	{
		return $this->currency;
	}

	/**
	 * If the promotion applies to the store_purchase - add a purchase_item for this promotion, otherwise remove the associated purchase item
	 * @param  Model_Store_Purchase $store_purchase
	 */
	public function update_store_purchase_items($applies, & $items)
	{
		$promo_item = Jam::build('purchase_item_promotion', array('reference' => $this));

		$items = array_filter($items, function($item) use ($promo_item) {
			return ! $item->is_same($promo_item);
		});

		if ($applies)
		{
			$items []= $promo_item;
		}
	}
}
