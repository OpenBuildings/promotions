<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *  Promotionable behavior
 * 
 * @package    promotions
 * @category   Behavior
 * @author     Yasen Yanev <yasen@openbuildings.com>
 * @author 		 Ivan Kerin <ivan@openbuildings.com>
 * @author 		 Haralan Dobrev <hdobrev@despark.com>
 * @copyright  (c) 2011-2013 Despark Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Kohana_Jam_Behavior_Promotionable extends Jam_Behavior {

	public function initialize(Jam_Meta $meta, $name) 
	{
		parent::initialize($meta, $name);

		$meta
			->fields(array(
				'_promo_code' => Jam::field('string', array('in_db' => FALSE)),
			))
			->associations(array(
				'promo_code' => Jam::association('hasone', array('inverse_of' => 'purchase')),
			));
	}


	public function model_before_check(Jam_Model $purchase)
	{
		if ($purchase->_promo_code)
		{
			// find promotion with this code
			$promo_code = Jam::all('promo_code')->where('code', '=', $purchase->_promo_code)->first();

			if ( ! $promo_code)
			{
				$purchase->errors()->add('_promo_code', 'invalid');
			}
			elseif ($promo_code->purchase AND $promo_code->purchase->id() != $purchase->id() AND $promo_code->purchase->is_paid())
			{
				$purchase->errors()->add('_promo_code', 'used');	
			}
			elseif ($promo_code->is_expired()) 
			{
				$purchase->errors()->add('_promo_code', 'expired');
			}
			elseif ( ! $promo_code->set('purchase', $purchase)->check())
			{
				$purchase->errors()->add('_promo_code', 'requirement', array(':error' => (string) $promo_code->errors()));
			}
			else
			{
				$purchase->promo_code = $promo_code;
			}
		}
		else
		{
			$purchase->promo_code = NULL;
		}
	}

	/**
	 * Check if we have at least one promotion from specified type
	 * @param  Jam_Model      $model
	 * @param  Jam_Event_Data $data
	 * @param  string $type
	 * @return bool
	 */
	public function model_call_promotion_type_exists(Jam_Model $model, Jam_Event_Data $data, $type)
	{
		foreach ($model->items('promotion') as $purchase_item) 
		{
			if ($purchase_item->reference->type == $type)
				return TRUE;
		}

		return FALSE;
	}

	/**
	 * Remove already attached promotions that do not apply for this purchase anymore
	 * @return void
	 */
	public function model_call_remove_unqualified_promotions(Jam_Model $model)
	{
		foreach ($purchase->items('promotion') as $purchase_item)
		{
			// remove promotion if it does not qualify anymore for this store purchase
			if ( ! $purchase_item->reference->applies_to($purchase_item->store_purchase))
			{
				$purchase_item->store_purchase->items->remove($purchase_item);
			}
		}
	}


	public function model_call_add_active_promotions(Jam_Model $model)
	{
		$active_promotions = Jam::all('promotion')
												 	->is_active();

		foreach ($active_promotions as $promotion) 
		{
			if ( ! $model->promotion_type_exists($promotion->type))
			{
				foreach ($model->store_purchase as $store_purchase) 
				{
					if ($promotion->applies_to($store_purchase))
					{
						$store_purchase->items->add($promotion);
					}
				}
			}
		}
	}
	
	public function model_call_update_promotions(Jam_Model $model, Jam_Event_Data $data)
	{
		$model->remove_unqualified_promotions();
		$model->add_active_promotions();
	}
}