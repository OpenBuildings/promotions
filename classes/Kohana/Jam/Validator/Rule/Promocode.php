<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Jam Validatior Rule
 *
 * @package    Jam
 * @category   Validation
 * @author     Ivan Kerin
 * @copyright  (c) 2011-2012 Despark Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Kohana_Jam_Validator_Rule_Promocode extends Jam_Validator_Rule {

	public function validate(Jam_Validated $model, $attribute, $value)
	{
		$promo_code = $this->valid_promo_code($value, $model);

		if ( ! $promo_code)
		{
			$model->errors()->add('promo_code_text', 'invalid');
		}
		elseif ( ! $promo_code->set('purchase', $model)->check())
		{
			$model->errors()->add('promo_code_text', 'requirement', array(':error' => (string) $promo_code->errors()));
		}
	}

	public function valid_promo_code($code, Model_Purchase $purchase)
	{
		return Jam::all('promo_code')
			->where('code', '=', $code)
			->not_expired()
			->available_for_purchase($purchase)
			->first();
	}
}