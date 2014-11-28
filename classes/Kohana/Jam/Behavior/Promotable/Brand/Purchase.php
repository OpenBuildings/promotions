<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * This behavior adds filter_items and update_items events to work with promotions for Model_Brand_Purchase
 *
 * @package    openbuildings\promotions
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Kohana_Jam_Behavior_Promotable_Brand_Purchase extends Jam_Behavior {

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

	/**
	 * Add a "promotion" filter:
	 *
	 * 	array('promotion' => 'promocode_giftcard')
	 *
	 * This will return only items that are "promotion_promocode_giftcard" models.
	 * Can be an array too.
	 *
	 * @param  Model_Brand_Purchase $brand_purchase
	 * @param  Jam_Event_Data       $data
	 * @param  array                $items
	 * @param  array                $filter
	 */
	public function filter_promotion_items(Model_Brand_Purchase $brand_purchase, Jam_Event_Data $data, array $items, array $filter)
	{
		$items = is_array($data->return) ? $data->return : $items;
		$filtered = array();

		foreach ($items as $item)
		{
			if (array_key_exists('promotion', $filter))
			{
				if ( ! Jam_Behavior_Promotable_Brand_Purchase::purchase_item_is_promotion($item, $filter['promotion']))
					continue;
			}

			if (array_key_exists('not_promotion', $filter))
			{
				if (Jam_Behavior_Promotable_Brand_Purchase::purchase_item_is_promotion($item, $filter['not_promotion']))
					continue;
			}

			$filtered [] = $item;
		}

		$data->return = $filtered;
	}

	/**
	 * Convert promotion name to model names
	 * @param  string|array $name
	 * @return array
	 */
	public static function promotion_model_names($name)
	{
		return array_map(function($name) {
			return 'promotion_'.$name;
		}, (array) $name);
	}

	/**
	 * Check if purchase item's reference is one of the given promotions
	 * @param  Model_Purchase_Item $item
	 * @param  string|array              $promotion
	 * @return boolean
	 */
	public static function purchase_item_is_promotion(Model_Purchase_Item $item, $promotion)
	{
		$model_names = Jam_Behavior_Promotable_Brand_Purchase::promotion_model_names($promotion);

		return in_array($item->reference_model, $model_names);
	}

	/**
	 * Iterate through available promotions and update its status on the brand purchase (does it apply or not)
	 *
	 * @param  Model_Brand_Purchase $brand_purchase
	 */
	public function update_promotion_items(Model_Brand_Purchase $brand_purchase)
	{
		$items = $brand_purchase->items->as_array();
		foreach ($this->available_promotions() as $promotion)
		{
			$promotion->update_brand_purchase_items($promotion->applies_to($brand_purchase), $items);
		}
		$brand_purchase->items = $items;
	}

	/**
	 * Return available (non expired) promotions
	 * @return Jam_Array_Model
	 */
	public function available_promotions()
	{
		return Jam::all('promotion')->not_expired()->order_by('priority', 'ASC');
	}
}
