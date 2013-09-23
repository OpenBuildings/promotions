<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 *  Promotionable behavior
 * 
 * @package    openbuildings\promotions
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Kohana_Jam_Behavior_Promotable_Store_Purchase extends Jam_Behavior {

	/**
	 * @codeCoverageIgnore
	 */
	public function initialize(Jam_Meta $meta, $name) 
	{
		parent::initialize($meta, $name);

		$meta
			->events()
				->bind('model.update_items', array($this, 'update_promotion_items'));
	}

	public function update_promotion_items(Model_Store_Purchase $store_purchase)
	{
		foreach ($this->available_promotions() as $promotion) 
		{
			$promotion->update_store_purchase($store_purchase);
		}
	}

	public function available_promotions()
	{
		return Jam::all('promotion')->not_expired();
	}
}