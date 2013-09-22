<?php

class Model_Store_Purchase extends Kohana_Model_Store_Purchase {

	public static function initialize(Jam_Meta $meta)
	{
		parent::initialize($meta);

		$meta
			->behaviors(array(
				'promotable_store_purchase' => Jam::behavior('promotable_store_purchase'),
			));
	}
}