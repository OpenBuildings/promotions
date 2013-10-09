<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 *  Promotionable behavior
 * 
 * @package    openbuildings\promotions
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Kohana_Jam_Behavior_Promotable_Purchase extends Jam_Behavior {

	/**
	 * @codeCoverageIgnore
	 */
	public function initialize(Jam_Meta $meta, $name) 
	{
		parent::initialize($meta, $name);

		$meta
			->fields(array(
				'promo_code_text' => Jam::field('string', array('in_db' => FALSE)),
			))
			->associations(array(
				'promo_code' => Jam::association('belongsto', array('inverse_of' => 'purchases')),
			))
			->validator('promo_code_text', array('purchase_promocode' => TRUE));
	}

	public function model_after_load(Model_Purchase $purchase)
	{
		if ($purchase->promo_code_id)
		{
			$purchase->retrieved('promo_code_text', $purchase->promo_code->code);
		}
	}

	public function model_after_check(Model_Purchase $purchase)
	{
		if ($purchase->changed('promo_code_text') AND ! $purchase->errors('promo_code_text'))
		{
			if ($purchase->promo_code_text) 
			{
				$purchase->promo_code = Jam::find('promo_code', $purchase->promo_code_text);
			}
			else
			{
				$purchase->promo_code = NULL;
			}
		}
	}
}