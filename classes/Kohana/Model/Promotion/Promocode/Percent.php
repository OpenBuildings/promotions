<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @package    openbuildings\promotions
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
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
		$brand_purchase_price = $purchase_item->get_insist('brand_purchase')->total_price('product');

		return $brand_purchase_price
			->multiply_by($this->amount)
			->multiply_by(-1);
	}
}
