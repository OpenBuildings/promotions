<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 *  Promotionable behavior
 * 
 * @package    promotions
 * @category   Behavior
 * @author 		 Ivan Kerin <ivan@openbuildings.com>
 * @copyright  (c) 2011-2013 Despark Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
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