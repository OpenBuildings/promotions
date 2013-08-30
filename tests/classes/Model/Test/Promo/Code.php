<?php

class Model_Test_Promo_Code extends Model_Promo_Code {

	public static function initialize(Jam_Meta $meta)
	{
		$meta->db(Kohana::TESTING);		
		parent::initialize($meta);

		$meta->association('promotion')->foreign_model = 'test_promotion';
	}
}