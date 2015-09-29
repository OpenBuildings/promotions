<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @package    openbuildings\promotions
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Kohana_Model_Collection_Promo_Code extends Jam_Query_Builder_Collection {

	public function not_expired($current_time = NULL)
	{
		return $this
			->where_open()
				->where('expires_at', '=', NULL)
				->or_where('expires_at', '>=', date('Y-m-d H:i:s', $current_time ?: time()))
			->where_close();
	}

	public function available()
	{
		return $this
			->join('purchases', 'LEFT')
			->where_open()
				->where('allow_multiple', '=', TRUE)
				->or_where('purchase.id', '=', NULL)
			->where_close();
	}

	public function available_for_purchase(Model_Purchase $purchase)
	{
		return $this
			->join('purchases', 'LEFT')
			->where_open()
				->where('allow_multiple', '=', TRUE)
				->or_where('purchase.id', '=', NULL)
				->or_where('purchase.id', '=', $purchase->id())
			->where_close();
	}
}
