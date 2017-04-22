<?php

use OpenBuildings\Monetary\Monetary;
use OpenBuildings\Monetary\Source_Static;

/**
 * @group model.promotion_promocode_percent
 *
 * @package Functest
 * @author Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Model_Promotion_Promocode_PercentTest extends Testcase_Promotions {

	/**
	 * @covers Model_Promotion_Promocode_Percent::price_for_purchase_item
	 */
	public function test_price_for_purchase_item()
	{
		$monetary = new Monetary(new Source_Static);
		$total_price = new Jam_Price(12, 'GBP', $monetary);

		$brand_purchase = $this->getMockBuilder('Model_Brand_Purchase')
			->setMethods(array('total_price'))
			->setConstructorArgs(array('brand_purchase'))
			->getMock();

		$brand_purchase
			->expects($this->once())
			->method('total_price')
			->with($this->equalTo('product'))
			->will($this->returnValue($total_price));

		$purchase_item = Jam::build('purchase_item_promotion', array('brand_purchase' => $brand_purchase));

		$promotion = Jam::build('promotion_promocode_percent', array(
			'amount' => 0.12,
		));

		$expected_price = new Jam_Price(-1.44, 'GBP', $monetary);

		$this->assertEquals($expected_price, $promotion->price_for_purchase_item($purchase_item));
	}


}
