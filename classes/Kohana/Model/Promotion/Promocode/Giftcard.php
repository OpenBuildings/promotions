<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @package    openbuildings\promotions
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Kohana_Model_Promotion_Promocode_Giftcard extends Model_Promotion_Promocode {

	/**
	 * @codeCoverageIgnore
	 */
	public static function initialize(Jam_Meta $meta)
	{
		parent::initialize($meta);

		$meta
			->fields(array(
				'amount' => Jam::field('price'),
				'requirement' => Jam::field('price'),
			))
			->validator('amount', array('price' => array('greater_than' => 0)))
			->validator('requirement', array('price' => array('greater_than_or_equal_to' => 0)));
	}

	public function validate_purchase(Model_Purchase $purchase)
	{
		$purchase_price = $purchase->total_price('product');

		if ($purchase_price->is(Jam_Price::LESS_THAN, $this->requirement))
		{
			$purchase->errors()->add('promo_code_text', 'requirement', array(':more_than' => $this->requirement->humanize()));
		}
	}

	public function price_for_purchase_item(Model_Purchase_Item $purchase_item)
	{
		$brand_purchases_count = $purchase_item->brand_purchase->purchase->brand_purchases->count();

		return $this->amount
			->monetary($purchase_item->monetary())
			->multiply_by(-1 / $brand_purchases_count);
	}
}
