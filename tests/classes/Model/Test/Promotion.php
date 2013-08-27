<?php

class Model_Test_Promotion extends Model_Promotion {

	public static function initialize(Jam_Meta $meta)
	{
		$meta->db(Kohana::TESTING);
		
		parent::initialize($meta);
	}
}