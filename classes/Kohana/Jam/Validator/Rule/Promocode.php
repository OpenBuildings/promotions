<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Validate for available and not expired promo_code_text
 *
 * @package    openbuildings\promotions
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Kohana_Jam_Validator_Rule_Promocode extends Jam_Validator_Rule {

	public function validate(Jam_Validated $model, $attribute, $value)
	{
		$promo_code = $this->valid_promo_code($value);

		if ( ! $promo_code)
		{
			$model->errors()->add('promo_code_text', 'invalid');
		}
	}

	public function valid_promo_code($code)
	{
		return Jam::all('promo_code')
			->where('code', '=', $code)
			->not_expired()
			->available()
			->first();
	}
}