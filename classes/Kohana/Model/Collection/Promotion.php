<?php defined('SYSPATH') OR die('No direct script access.');

class Kohana_Model_Collection_Promotion extends Jam_Query_Builder_Collection {

	public function is_active()
	{
		return $this->where('expires_at', '>=', DB::expr('now'));
	}
}