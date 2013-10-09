<?php

/**
 * Functest_TestsTest 
 *
 * @group model.collection.promo_code
 * 
 * @package Functest
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Model_Collection_Promo_CodeTest extends Testcase_Promotions {

	/**
	 * @covers Model_Collection_Promo_Code::not_expired
	 */
	public function test_not_expired()
	{
		$time = strtotime('2013-02-02');

		$sql = (string) Jam::all('promo_code')->not_expired($time);

		$expected_sql = "SELECT `promo_codes`.* FROM `promo_codes` WHERE (`promo_codes`.`expires_at` IS NULL OR `promo_codes`.`expires_at` >= '2013-02-02 00:00:00')";

		$this->assertEquals($expected_sql, $sql);
	}

	/**
	 * @covers Model_Collection_Promo_Code::available_for_purchase
	 */
	public function test_available_for_purchase()
	{
		$purchase = Jam::build('purchase', array('id' => 10));

		$sql = (string) Jam::all('promo_code')->available_for_purchase($purchase);

		$expected_sql = "SELECT `promo_codes`.* FROM `promo_codes` LEFT JOIN `purchases` ON (`purchases`.`promo_code_id` = `promo_codes`.`id`) WHERE (`promo_codes`.`allow_multiple` = '1' OR `purchases`.`id` IS NULL OR `purchases`.`id` = 10)";

		$this->assertEquals($expected_sql, $sql);
	}

	/**
	 * @covers Model_Collection_Promo_Code::available
	 */
	public function test_available()
	{
		$sql = (string) Jam::all('promo_code')->available();

		$expected_sql = "SELECT `promo_codes`.* FROM `promo_codes` LEFT JOIN `purchases` ON (`purchases`.`promo_code_id` = `promo_codes`.`id`) WHERE (`promo_codes`.`allow_multiple` = '1' OR `purchases`.`id` IS NULL)";

		$this->assertEquals($expected_sql, $sql);
	}
}