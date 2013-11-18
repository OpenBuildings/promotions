<?php

/**
 * @group model
 * @group model.purchase_item_promotion
 */
class Model_Purchase_Item_PromotionTest extends Testcase_Promotions {

	/**
	 * @covers Model_Purchase_Item_Promotion::initialize
	 */
	public function test_initialize()
	{
		$meta = Jam::meta('purchase_item_promotion');
		$this->assertSame('purchase_items', $meta->table());
		$this->assertTrue($meta->field('is_payable')->default);
		$this->assertTrue($meta->field('is_discount')->default);
	}

	/**
	 * @covers Model_Purchase_Item_Promotion::get_price
	 */
	public function test_get_price()
	{
		$mock = $this->getMock('stdClass', array(
			'price_for_purchase_item'
		));

		$purchase_item = $this->getMock('Model_Purchase_Item_Promotion', array(
			'get_reference_paranoid'
		), array(
			'purchase_item_promotion'
		));

		$purchase_item
			->expects($this->once())
			->method('get_reference_paranoid')
			->will($this->returnValue($mock));

		$mock
			->expects($this->once())
			->method('price_for_purchase_item')
			->with($purchase_item)
			->will($this->returnValue(10.25));

		$this->assertSame(10.25, $purchase_item->get_price());
	}
}