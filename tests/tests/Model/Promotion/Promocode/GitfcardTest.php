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

	public function data_validate_purchase()
	{
		$monetary = new Monetary(new Source_Static);

		return array(
			array(new Jam_Price(5, 'GBP', $monetary), array('promo_code_text' => array('requirement' => array(':more_than' => '£10.00')))),
			array(new Jam_Price(20, 'GBP', $monetary), FALSE),
		);
	}

	/**
	 * @dataProvider data_validate_purchase
	 * @covers Model_Promotion_Promocode_Giftcard::validate_purchase
	 */
	public function test_validate_purchase($total_price, $expected_errors)
	{
		$purchase = $this->getMock('Model_Purchase', array('total_price'), array('purchase'));

		$promotion = Jam::build('promotion', array(
			'currency' => 'GBP',
			'requirement' => 10,
			'model' => 'promotion_promocode_giftcard'
		));

		$purchase
			->expects($this->once())
			->method('total_price')
			->with($this->equalTo('product'))
			->will($this->returnValue($total_price));

		$promotion->validate_purchase($purchase);

		if ($expected_errors) 
		{
			$this->assertFalse($purchase->is_valid());
			$this->assertEquals($expected_errors, $purchase->errors()->as_array());
		}
		else
		{
			$this->assertTrue($purchase->is_valid());
		}
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
			'currency' => 'GBP',
		));

		$expected_price = new Jam_Price(-20, 'GBP', $monetary);

		$this->assertEquals($expected_price, $promotion->price_for_purchase_item($purchase_item));
	}


}