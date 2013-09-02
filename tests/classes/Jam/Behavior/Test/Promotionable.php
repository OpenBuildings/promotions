<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *  Test Promotionable behavior
 * 
 * @package    promotions
 * @category   Behavior
 * @author     Yasen Yanev <yasen@openbuildings.com>
 * @copyright  (c) 2011-2013 Despark Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Jam_Behavior_Test_Promotionable extends Kohana_Jam_Behavior_Promotionable {

	const FREE_SHIPPING_THRESHOLD_GBP = 69;
	const FREE_SHIPPING_THRESHOLD_EUR = 99;

	protected $_inverse_of_association = 'test_purchases';
	protected $_promo_code_association = 'test_promo_code';

	public function initialize(Jam_Meta $meta, $name) 
	{
		parent::initialize($meta, $name);

		$meta
			->fields(array(
				'_promo_code' => Jam::field('string', array('in_db' => FALSE)),
			))
			->associations(array(
				$this->_promo_code_association => Jam::association('hasone', array('inverse_of' => $this->_inverse_of_association)),
			));
	}
}