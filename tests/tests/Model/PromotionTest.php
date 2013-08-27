<?php

use OpenBuildings\Monetary\Monetary;

/**
 * Functest_TestsTest 
 *
 * @group model.promotion
 * 
 * @package Functest
 * @author Yasen Yanev
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Model_PromotionTest extends Testcase_Promotions {
	
	public function setUp()
	{
		parent::setUp();

		$this->purchase = Jam::build('test_purchase', array(
			'payment' => array(
				'method' => 'emp',
				'status' => 'paid',
				'raw_response' => array('successful'),
			),
			'store_purchases' => array(
				array(
					'store' => 1,
					'items' => array(
						array(
							'price' => 200,
							'type' => 'product',
							'quantity' => 1,
							'reference' => array('test_product' => 1),
						),
						array(
							'price' => 200,
							'type' => 'product',
							'quantity' => 1,
							'reference' => array('test_variation' => 1),
						),
					),
				)
			)
		));
	}
	/**
	 * @covers Model_Promotion::discount
	 */
	public function test_discount()
	{
		$promotion = Jam::find('test_promotion', 3);
		$promotion_min_purchase = Jam::find('test_promotion', 1);

		$this->assertEquals(10, $promotion->discount($this->purchase->store_purchases[0]));
		$this->assertEquals(20, $promotion_min_purchase->discount($this->purchase->store_purchases[0])); // 5% of products total price 
	}

	/**
	 * @covers Model_Promotion::applies_to
	 */
	public function test_applies_to()
	{
		$promotion = Jam::find('test_promotion', 3);
		$promotion_min_purchase = Jam::find('test_promotion', 2);

		$this->assertEquals(FALSE, $promotion->applies_to($this->purchase->store_purchases[0]));
		$this->assertEquals(TRUE, $promotion_min_purchase->applies_to($this->purchase->store_purchases[0]));
	}
}