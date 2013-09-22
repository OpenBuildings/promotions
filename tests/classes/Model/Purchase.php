<?php

class Model_Purchase extends Kohana_Model_Purchase {

	public static function initialize(Jam_Meta $meta)
	{
		parent::initialize($meta);

		$meta
			->behaviors(array(
				'promotable_purchase' => Jam::behavior('promotable_purchase'),
			));
	}
}