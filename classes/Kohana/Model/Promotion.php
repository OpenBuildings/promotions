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
				)),
				'promo_codes' => Jam::association('hasmany', array(
					'inverse_of' => 'promotion',
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
	public function applies_to(Model_Brand_Purchase $purchase)
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

	public function is_expired()
	{
		return ($this->expires_at !== NULL AND strtotime($this->expires_at) < time());
	}

	/**
	 * If the promotion applies to the brand_purchase - add a purchase_item for this promotion, otherwise remove the associated purchase item
	 * @param  Model_Brand_Purchase $brand_purchase
	 */
	public function update_brand_purchase_items($applies, & $items)
	{
		$promo_item = Jam::build('purchase_item_promotion', array('reference' => $this));

		$has_existing_promotion = false;
		foreach ($items as $index => $item){
			if ($item->is_same($promo_item)) {
				$has_existing_promotion = true;
				if ( ! $applies) {
					unset($items[$index]);
				}
			}
		}

		if ($applies && ! $has_existing_promotion) {
			$items[] = $promo_item;
		}
	}
}
