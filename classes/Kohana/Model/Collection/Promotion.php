<?php defined('SYSPATH') OR die('No direct script access.');

class Kohana_Model_Collection_Promotion extends Jam_Query_Builder_Collection {

	public function not_expired($current_time = NULL)
	{
		return $this
			->where_open()
				->where('expires_at', '=', NULL)
				->or_where('expires_at', '>=', date('Y-m-d H:i:s', $current_time ?: time()))
			->where_close();
	}
}