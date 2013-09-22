<?php

use OpenBuildings\Monetary\Monetary;
use OpenBuildings\Monetary\Source_Static;

/**
 * @group model.promotion_promocode_giftcard
 * 
 * @package Functest
 * @author Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Model_Promotion_Promocode_GiftcardTest extends Testcase_Promotions {

	/**
	 * @covers Model_Promotion_Promocode_Giftcard::applies_to
	 */
	public function test_applies_to()
	{
		$monetary = new Monetary(new Source_Static);

		$store_purchase = $this->getMock('Model_Store_Purchase', array('total_price'), array('store_purchase'));

		$promotion = $this->getMock('Model_Promotion_Promocode_Giftcard', array('matches_store_purchase_promo_code'), array('promotion_promocode_giftcard'));

		$promotion->set(array(
			'currency' => 'GBP',
			'requirement' => 10,
		));

		$promotion
			->expects($this->exactly(4))
			->method('matches_store_purchase_promo_code')
			->will($this->onConsecutiveCalls(FALSE, TRUE, TRUE, TRUE));

		$store_purchase
			->expects($this->exactly(3))
			->method('total_price')
			->with($this->equalTo('product'))
			->will($this->onConsecutiveCalls(
				new Jam_Price(5, 'GBP', $monetary),
				new Jam_Price(10, 'USD', $monetary),
				new Jam_Price(20, 'GBP', $monetary)
			));

		$this->assertFalse($promotion->applies_to($store_purchase));
		$this->assertFalse($promotion->applies_to($store_purchase));
		$this->assertFalse($promotion->applies_to($store_purchase));
		$this->assertTrue($promotion->applies_to($store_purchase));
	}

	/**
	 * @covers Model_Promotion_Promocode_Giftcard::price_for_purchase_item
	 */
	public function test_price_for_purchase_item()
	{
		$monetary = new Monetary(new Source_Static);

		$purchase_item = $this->getMock('Model_Purchase_Item', array('monetary'), array('purchase_item'));

		$purchase_item
			->expects($this->once())
			->method('monetary')
			->will($this->returnValue($monetary));

		$promotion = Jam::build('promotion_promocode_giftcard', array(
			'amount' => 20, 
			'currency' => 'GBP'
		));

		$expected_price = new Jam_Price(-20, 'GBP', $monetary);

		$this->assertEquals($expected_price, $promotion->price_for_purchase_item($purchase_item));
	}


}