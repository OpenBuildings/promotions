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
				->bind('model.filter_items', array($this, 'filter_promotion_items'))
				->bind('model.update_items', array($this, 'update_promotion_items'));
	}

	public function filter_promotion_items(Model_Store_Purchase $store_purchase, Jam_Event_Data $data, array $items, array $filter)
	{
		$items = is_array($data->return) ? $data->return : $items;
		$filtered = array();

		foreach ($items as $item)
		{
			if (array_key_exists('promotion', $filter))
			{
				if ($item->type !== 'promotion') 
					continue;

				$model_names = array_map(function($name) {
					return 'promotion_'.$name;
				}, (array) $filter['promotion']);

				if ( ! in_array($item->get_insist('reference')->model, $model_names))
					continue;
			}

			$filtered [] = $item;
		}

		$data->return = $filtered;
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