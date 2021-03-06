<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Add promo_code assocition and promo_code_text field with associated validation
 *
 * When you assign promo_code_text then it tries to find the appropriate promo_code and assign it.
 * When you assign promo_code object, its code can be retrieved from promo_code_text (two way binding)
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

	/**
	 * If there is a promo_code object, load it into the promo_code_text
	 * @param  Model_Purchase $purchase
	 */
	public function model_after_load(Model_Purchase $purchase)
	{
		if ($purchase->promo_code_id)
		{
			$promo_code = Jam_Behavior_Paranoid::with_filter(
				Jam_Behavior_Paranoid::ALL,
				function () use ($purchase) {
					return $purchase->promo_code;
				}
			);
			$purchase->retrieved('promo_code_text', $promo_code->code);
		}
	}

	/**
	 * If there is a new value in promo_code_text, try to load promo_code object.
	 * If the new value is NULL, remove it
	 * @param  Model_Purchase $purchase
	 */
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
