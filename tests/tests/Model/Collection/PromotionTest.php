<?php

/**
 * Functest_TestsTest
 *
 * @group model.collection.promotion
 *
 * @package Functest
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Model_Collection_PromotionTest extends Testcase_Promotions {

	/**
	 * @covers Model_Collection_Promotion::not_expired
	 */
	public function test_not_expired()
	{
		$time = strtotime('2013-02-02');

		$sql = (string) Jam::all('promotion')->not_expired($time);

		$expected_sql = "SELECT `promotions`.* FROM `promotions` WHERE (`promotions`.`expires_at` IS NULL OR `promotions`.`expires_at` >= '2013-02-02 00:00:00')";

		$this->assertEquals($expected_sql, $sql);
	}
}