<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *  Model_Test_Promotion
 * 
 * @package    promotions
 * @category   Model
 * @author     Yasen Yanev <yasen@openbuildings.com>
 * @copyright  (c) 2011-2013 Despark Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Model_Test_Promotion extends Model_Promotion {

	const TYPE_ELLEVISA_DISCOUNT = 'ellevisa_discount';
	const TYPE_FREE_SHIPPING = 'free_shipping';
	const ELLEVISA_PROMO_CODE = 'TESTVISA';

	public static function initialize(Jam_Meta $meta)
	{
		$meta->db(Kohana::TESTING);
		parent::initialize($meta);
	}

	public function price_ellevisa_discount(Model_Purchase_Item $purchase_item)
	{
		
	}

	public function price_free_shipping(Model_Purchase_Item $purchase_item)
	{
		// return -$purchase_item->store_purchase->total_price('shipping');
		return -5;
	}

	public function applies_to_ellevisa_discount(Model_Purchase_Item $purchase_item)
	{
		if ($purchase_item->purchase_insist()->_promo_code === Model_Test_Promotion::ELLEVISA_PROMO_CODE)
		{
			return ($this->type == Model_Test_Promotion::TYPE_ELLEVISA_DISCOUNT AND ! $this->expires_at OR strtotime($this->expires_at) >= time());
		}

		return FALSE;
	}

	public function applies_to_free_shipping(Model_Purchase_Item $purchase_item)
	{
		/**
		 * @todo  uncomment shipping price check when shipping module is implemented
		 */
		// if ( ! $purchase_item->purchase_insist()->total_price('shipping'))
		// 	return FALSE;
		
		if ($this->type == Model_Test_Promotion::TYPE_FREE_SHIPPING)
		{
			$purchase = $purchase_item->purchase_insist();
			$country = $purchase->country;
			$currency = ($purchase->currency === 'EUR') ? 'EUR' : 'GBP';

			return ($country === 'Europe' AND $purchase->total_price('product') >= (float) $this->requirement AND $currency == $this->currency);
		}
		
		return FALSE;
	}
}