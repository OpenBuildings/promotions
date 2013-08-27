<?php defined('SYSPATH') OR die('No direct script access.');

class Kohana_Model_Promo_Code extends Jam_Model {

	const ORIGIN_SUCCESSFUL_PURCHASE = 'successful-purchase';
	
	const ORIGIN_SUCCESSFUL_PURCHASE_FRIEND = 'successful-purchase-friend';

	const ORIGIN_ABANDONED_CART = 'abandoned-cart';

	const ORIGIN_EVENT = 'event';

	public static function initialize(Jam_Meta $meta)
	{
		$meta
			->name_key('code')
			->behaviors(array(
				'tokenable' => Jam::behavior('tokenable', array(
					'field' => 'code',
					'uppercase' => TRUE,
				)),
			))
			->associations(array(
				'purchase' => Jam::association('belongsto', array('inverse_of' => 'promo_code')),
				'promotion' => Jam::association('belongsto', array('inverse_of' => 'promo_codes'))
			))
			->fields(array(
				'id' => Jam::field('primary'),
				'code' => Jam::field('string'),
				'origin' => Jam::field('string'),
				'created_at' => Jam::field('timestamp', array('auto_now_create' => TRUE, 'format' => 'Y-m-d H:i:s')),
				'expires_at' => Jam::field('timestamp', array('format' => 'Y-m-d H:i:s'))
			))
			->validator('promotion', 'origin', array('present' => TRUE));
	}

	public function is_expired()
	{
		return ($this->expires_at AND strtotime($this->expires_at) < time());
	}
}
