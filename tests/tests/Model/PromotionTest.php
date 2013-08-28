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
			'currency' => 'EUR',
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
	 * @covers Model_Promotion::price
	 */
	public function test_price()
	{
		$promotion = Jam::find('test_promotion', 3);
		$promotion_min_purchase = Jam::find('test_promotion', 1);
		$purchase_item = $this->purchase->store_purchases[0]->items[0];

		$this->assertEquals(-10, $promotion->price($purchase_item));
		$this->assertEquals(-20, $promotion_min_purchase->price($purchase_item)); // 5% of products total price 
	}

	/**
	 * @covers Model_Promotion::applies_to
	 */
	public function test_applies_to()
	{
		$promotion = Jam::find('test_promotion', 3);
		$promotion_min_purchase = Jam::find('test_promotion', 2);
		$purchase_item = $this->purchase->store_purchases[0]->items[0];

		$this->assertEquals(FALSE, $promotion->applies_to($purchase_item));
		$this->assertEquals(TRUE, $promotion_min_purchase->applies_to($purchase_item));
	}

	/**
	 * @covers Model_Promotion::currency
	 */
	public function test_promotion_with_currency()
	{
		$promotion = Jam::find('test_promotion', 3);
		$this->purchase->store_purchases[0]->items->build(array(
			'reference' => $promotion,
			'quantity' => 1,
			'type' => 'promotion',
		));

		$promotion_purchase_item = $this->purchase->store_purchases[0]->items[2];
		$this->assertEquals(-11.615750958299, $promotion_purchase_item->price($promotion_purchase_item));
	}

	public function test_promotion_no_currency($value='')
	{
		$promotion2 = Jam::build('test_promotion', array('value' => 15, 'type' => 'discount'));

		$this->purchase->store_purchases[0]->items->build(array(
			'reference' => $promotion2,
			'quantity' => 1,
			'type' => 'promotion',
		));

		$promotion_purchase_item = $this->purchase->store_purchases[0]->items[2];
		$this->assertEquals(-15, $promotion_purchase_item->price($promotion_purchase_item));
	}
}