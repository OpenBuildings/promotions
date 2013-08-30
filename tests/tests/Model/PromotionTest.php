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
			'country' => 'Europe',
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

		$this->purchase->monetary()->source(new OpenBuildings\Monetary\Source_Static);
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
		$this->assertEquals(FALSE, $promotion_min_purchase->applies_to($purchase_item));

		$this->purchase->_promo_code = '8BZD45';
		$valid_purchase = $this->purchase->check();
		$this->assertEquals(TRUE, $valid_purchase);

		$this->assertEquals(TRUE, $promotion->applies_to($purchase_item));
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
		$this->assertEquals(-11.909724289883, $promotion_purchase_item->price($promotion_purchase_item));
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

	public function test_promotion_ellevisa()
	{

	}

	public function test_applies_to_free_shipping()
	{
		$promotion = Jam::find('test_promotion', 5);
		$store_purchase = $this->purchase->store_purchases[0];
		$this->assertEquals(TRUE, $promotion->applies_to($store_purchase->items[0]));

		$store_purchase->items[0]->price = 98;
		unset($store_purchase->items[1]);

		$this->assertEquals(FALSE, $promotion->applies_to($store_purchase->items[0]));

		$store_purchase->items[0]->price = 100;
		$this->purchase->country = 'United States';
		$this->assertEquals(FALSE, $promotion->applies_to($store_purchase->items[0]));
	}

	public function test_update_promotions()
	{
		$this->purchase->update_promotions();

		foreach ($this->purchase->items('promotion') as $purchase_item) 
		{
			$this->assertContains($purchase_item->reference->id(), array(5));
		}


		$promo_code = Jam::find('test_promo_code', 3);
		// add a promo code to the purchase
		$this->purchase->_promo_code = $promo_code->code;
		$this->purchase->check();

		$this->purchase->update_promotions();

		foreach ($this->purchase->items('promotion') as $purchase_item) 
		{
			$this->assertContains($purchase_item->reference->id(), array(5, 3));
		}
	}
}