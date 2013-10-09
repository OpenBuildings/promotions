<?php

/**
 * @group model.promo_code
 * 
 * @package Functest
 * @author Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Model_Promo_CodeTest extends Testcase_Promotions {

	public function test_promocode_find()
	{
		$find_by_id = Jam::all('promo_code')->where(':unique_key', '=', 1);
		$expected_sql = "SELECT `promo_codes`.* FROM `promo_codes` WHERE `promo_codes`.`id` = 1";

		$this->assertEquals($expected_sql, (string) $find_by_id);

		$find_by_string = Jam::all('promo_code')->where(':unique_key', '=', 'ads');
		$expected_sql = "SELECT `promo_codes`.* FROM `promo_codes` WHERE `promo_codes`.`code` = 'ads'";

		$this->assertEquals($expected_sql, (string) $find_by_string);

		$find_by_code = Jam::all('promo_code')->where(':unique_key', '=', '621ZWM');
		$expected_sql = "SELECT `promo_codes`.* FROM `promo_codes` WHERE `promo_codes`.`code` = '621ZWM'";

		$this->assertEquals($expected_sql, (string) $find_by_code);
	}

	/**
	 * @covers Model_Promo_Code::validate_purchase
	 */
	public function test_validate_purchase()
	{
		$purchase = Jam::build('purchase');
		
		$promotion = $this->getMock('Model_Promotion', array('validate_purchase'), array('promotion'));
		$promotion
			->expects($this->once())
			->method('validate_purchase')
			->with($this->identicalTo($purchase));

		$promo_code = Jam::build('promo_code', array('promotion' => $promotion));

		$promo_code->validate_purchase($purchase);
	}
}