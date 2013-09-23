<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @package    openbuildings\promotions
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @author     Yasen Yanev <yasen@openbuildings.com>
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Kohana_Model_Promo_Code extends Jam_Model {
	
	/**
	 * @codeCoverageIgnore
	 */
	public static function initialize(Jam_Meta $meta)
	{
		$meta
			->name_key('code')
			->unique_key(function($key){
				return is_string($key) ? 'code' : 'id';
			})
			->behaviors(array(
				'tokenable' => Jam::behavior('tokenable', array(
					'field' => 'code',
					'uppercase' => TRUE,
				)),
			))
			->associations(array(
				'purchases' => Jam::association('hasmany', array('inverse_of' => 'promo_code', 'foreign_model' => 'purchase')),
				'promotion' => Jam::association('belongsto', array('inverse_of' => 'promo_codes'))
			))
			->fields(array(
				'id' => Jam::field('primary'),
				'origin' => Jam::field('string'),
				'allow_multiple' => Jam::field('boolean'),
				'created_at' => Jam::field('timestamp', array('auto_now_create' => TRUE, 'format' => 'Y-m-d H:i:s')),
				'expires_at' => Jam::field('timestamp', array('format' => 'Y-m-d H:i:s'))
			))
			->validator('promotion', 'origin', array('present' => TRUE));
	}

	public function validate_purchase(Model_Purchase $purchase)
	{
		$this->get_insist('promotion')->validate_purchase($purchase);
	}
}
