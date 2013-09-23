<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @package    openbuildings\promotions
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @author     Yasen Yanev <yasen@openbuildings.com>
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Kohana_Model_Promotion_Promocode extends Model_Promotion {

	/**
	 * @codeCoverageIgnore
	 */
	public static function initialize(Jam_Meta $meta)
	{
		parent::initialize($meta);

		$meta
			->associations(array(
				'promo_codes' => Jam::association('hasmany', array(
					'inverse_of' => 'promotion',
					'foreign_key' => 'promotion_id',
				)),
			));
	}

	public function applies_to(Model_Store_Purchase $store_purchase)
	{
		return $this->matches_store_purchase_promo_code($store_purchase);
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function validate_purchase(Model_Purchase $store_purchase)
	{
		// extend this to add custom validation for the purchase (or promocode)
	}

	public function matches_store_purchase_promo_code(Model_Store_Purchase $store_purchase)
	{
		$promo_code = $store_purchase->get_insist('purchase')->promo_code;

		if ( ! $promo_code)
			return FALSE;

		return $this->has_promo_code($promo_code);
	}

	public function has_promo_code(Model_Promo_Code $promo_code)
	{
		return $this->promo_codes->where('id', '=', $promo_code->id())->count_all() > 0;
	}
}
