<?php

/**
 * @group jam.behavior.promotable_brand_purchase
 *
 * @package Functest
 * @author Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Jam_Behavior_Promotable_Brand_PurchaseTest extends Testcase_Promotions {

	/**
	 * @covers Jam_Behavior_Promotable_Brand_Purchase::available_promotions
	 */
	public function test_available_promotions()
	{
		$behavior = Jam::behavior('promotable_brand_purchase');

		$promotions = $behavior->available_promotions();

		$this->assertEquals(array(1, 2), $promotions->ids());

		Jam::find('promotion', 1)->update_fields(array('expires_at' => date('Y-m-d H:i:s',strtotime('-2days'))));

		$promotions = $behavior->available_promotions();

		$this->assertEquals(array(2), $promotions->ids());
	}

	/**
	 * @covers Jam_Behavior_Promotable_Brand_Purchase::update_promotion_items
	 */
	public function test_update_promotion_items()
	{
		$brand_purchase = Jam::build('brand_purchase');

		$promotion = $this->getMockBuilder('Model_Promotion')
			->setMethods(array('update_brand_purchase_items', 'applies_to'))
			->setConstructorArgs(array('promotion'))
			->getMock();

		$promotion
			->expects($this->at(1))
			->method('update_brand_purchase_items')
			->with($this->equalTo(TRUE), $this->equalTo(array()));

		$promotion
			->expects($this->at(3))
			->method('update_brand_purchase_items')
			->with($this->equalTo(FALSE), $this->equalTo(array()));

		$promotion
			->expects($this->exactly(2))
			->method('applies_to')
			->will($this->onConsecutiveCalls(TRUE, FALSE));

		$behavior = $this->getMockBuilder('Jam_Behavior_Promotable_Brand_Purchase')
			->setMethods(array('available_promotions'))
			->getMock();

		$behavior
			->expects($this->once())
			->method('available_promotions')
			->will($this->returnValue(array($promotion, $promotion)));

		$behavior->update_promotion_items($brand_purchase);
	}


	/**
	 * @covers Jam_Behavior_Promotable_Brand_Purchase::filter_promotion_items
	 */
	public function test_filter_promotion_items()
	{
		$promocode_giftcard = Jam::build('promotion_promocode_giftcard', array('id' => 20));
		$promocode_percent = Jam::build('promotion_promocode_percent', array('id' => 21));

		$brand_purchase = Jam::find('brand_purchase', 2);

		$brand_purchase->items->add(Jam::build('purchase_item_promotion', array('id' => 12, 'reference' => $promocode_giftcard, 'is_discount' => TRUE, 'is_payable' => TRUE)));

		$brand_purchase->items->add(Jam::build('purchase_item_promotion', array('id' => 16, 'reference' => $promocode_percent, 'is_discount' => TRUE, 'is_payable' => TRUE)));

		$items = $brand_purchase->items(array('promotion' => 'promocode_giftcard'));
		$this->assertEquals(array(12), $this->ids($items));

		$items = $brand_purchase->items(array('promotion' => 'promocode_percent'));
		$this->assertEquals(array(16), $this->ids($items));

		$items = $brand_purchase->items(array('promotion' => array('promocode_percent', 'promocode_giftcard')));
		$this->assertEquals(array(12, 16), $this->ids($items));

		$items = $brand_purchase->items(array('promotion', 'not_promotion' => 'promocode_giftcard'));
		$this->assertEquals(array(16), $this->ids($items));
	}

	public function data_promotion_model_names()
	{
		return array(
			array('promocode', array('promotion_promocode')),
			array(array('promocode'), array('promotion_promocode')),
			array(array('promocode', 'promocode_giftcard'), array('promotion_promocode', 'promotion_promocode_giftcard')),
		);
	}

	/**
	 * @dataProvider data_promotion_model_names
	 * @covers Jam_Behavior_Promotable_Brand_Purchase::promotion_model_names
	 */
	public function test_promotion_model_names($promotion, $expected)
	{
		$this->assertEquals($expected, Jam_Behavior_Promotable_Brand_Purchase::promotion_model_names($promotion));
	}

	public function data_purchase_item_is_promotion()
	{
		return array(
			array('product', array('promocode'), FALSE),
			array('promotion', array('promocode'), FALSE),
			array('promotion_promocode', array('promocode'), TRUE),
			array('promotion_promocode', array('promocode_giftcard'), FALSE),
		);
	}

	/**
	 * @dataProvider data_purchase_item_is_promotion
	 * @covers Jam_Behavior_Promotable_Brand_Purchase::purchase_item_is_promotion
	 */
	public function test_purchase_item_is_promotion($reference_model, $promotion, $expected)
	{
		$item = Jam::build('purchase_item_promotion', array('reference_model' => $reference_model));

		$this->assertEquals($expected, Jam_Behavior_Promotable_Brand_Purchase::purchase_item_is_promotion($item, $promotion));
	}
}
