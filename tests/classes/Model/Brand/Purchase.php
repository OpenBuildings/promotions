<?php

class Model_Brand_Purchase extends Kohana_Model_Brand_Purchase {

	public static function initialize(Jam_Meta $meta)
	{
		parent::initialize($meta);

		$meta
			->behaviors(array(
				'promotable_brand_purchase' => Jam::behavior('promotable_brand_purchase'),
			));
	}
}
