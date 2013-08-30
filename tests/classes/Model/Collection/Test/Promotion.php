<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Collection_Test_Promotion extends Kohana_Model_Collection_Promotion {

	public function is_active()
	{
		return $this->where('expires_at', 'IS', NULL)->or_where('expires_at', '>=', DB::expr('NOW()'));
	}
}