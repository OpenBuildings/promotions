<?php

/**
 * @group model.promotion
 *
 * @package Functest
 * @author Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Model_PromotionTest extends Testcase_Promotions {

	/**
	 * @covers Model_Promotion::currency
	 */
	public function test_currency()
	{
		$promotion = Jam::build('promotion', array('currency' => 'EUR'));

		$this->assertEquals('EUR', $promotion->currency());

		$promotion->currency = 'GBP';

		$this->assertEquals('GBP', $promotion->currency());
	}

	/**
	 * @covers Model_Promotion::price_for_purchase_item
	 */
	public function test_price_for_purchase_item()
	{
        $this->expectException(Kohana_Exception::class);
        $this->expectExceptionMessage('Not a valid promotion');

		$promotion = Jam::build('promotion');

		$promotion->price_for_purchase_item(Jam::build('purchase_item_promotion'));
	}

	/**
	 * @covers Model_Promotion::price_for_purchase_item
	 */
	public function test_applies_to()
	{
        $this->expectException(Kohana_Exception::class);
        $this->expectExceptionMessage('Not a valid promotion');

		$promotion = Jam::build('promotion');

		$promotion->applies_to(Jam::build('brand_purchase'));
	}

	public function data_is_expired()
	{
		return array(
			array(NULL, FALSE),
			array('-1 day', TRUE),
			array('+1 day', FALSE),
		);
	}

	/**
	 * @covers Model_Promotion::is_expired
	 * @dataProvider data_is_expired
	 */
	public function test_is_expired($expires_at, $expected)
	{
		$promotion = Jam::build('promotion', array('expires_at' => $expires_at));

		$this->assertEquals($expected, $promotion->is_expired());
	}


	/**
	 * @covers Model_Promotion::build_purchase_item
	 */
	public function test_build_purchase_item()
	{
		$promotion = Jam::build('promotion')->load_fields(array('id' => 10));

		$purchase_item = $promotion->build_purchase_item();

		$this->assertSame($promotion, $purchase_item->reference);
		$this->assertEquals('promotion', $purchase_item->type());
		$this->assertTrue($purchase_item->is_payable);
	}

	public function test_update_brand_purchase()
	{
		$brand_purchase = $this->getMockBuilder('Model_Brand_Purchase')
			->setMethods(array('search_same_item'))
			->setConstructorArgs(array('brand_purchase'))
			->getMock();

		$brand_purchase->items = array(
			array('id' => 10, 'model' => 'purchase_item_product'),
			array('id' => 15, 'model' => 'purchase_item_product'),
		);

		$promotion = Jam::build('promotion')->load_fields(array('id' => 10));

		$items = $brand_purchase->items->as_array();

		// applies_to = TRUE, offset = 0
		$promotion->update_brand_purchase_items(TRUE, $items);

		$this->assertCount(3, $items);
		$this->assertSame($promotion, $items[2]->reference);

		$previous_promotion_item = $items[2];

		// applies_to = TRUE, offset = NULL
		$promotion->update_brand_purchase_items(TRUE, $items);

		$this->assertCount(3, $items);
		$this->assertSame($previous_promotion_item, $items[2], 'Failed asserting that promotion item is reused on update');
		$this->assertSame($promotion, $items[2]->reference);

		// applies_to = FALSE, offset = 1
		$promotion->update_brand_purchase_items(FALSE, $items);

		$this->assertCount(2, $items);
		$this->assertFalse(isset($items[2]));

		// applies_to = FALSE, offset = NULL
		$promotion->update_brand_purchase_items(FALSE, $items);

		$this->assertCount(2, $items);
	}
}
