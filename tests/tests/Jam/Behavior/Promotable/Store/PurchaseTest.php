<?php

/**
 * @group jam.behavior.promotable_store_purchase
 * 
 * @package Functest
 * @author Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Jam_Behavior_Promotable_Store_PurchaseTest extends Testcase_Promotions {

	/**
	 * @covers Jam_Behavior_Promotable_Store_Purchase::available_promotions
	 */
	public function test_available_promotions()
	{
		$behavior = Jam::behavior('promotable_store_purchase');

		$promotions = $behavior->available_promotions();

		$this->assertEquals(array(1, 2), $promotions->ids());

		Jam::find('promotion', 1)->update_fields(array('expires_at' => strtotime('-2days')));

		$promotions = $behavior->available_promotions();

		$this->assertEquals(array(2), $promotions->ids());
	}

	/**
	 * @covers Jam_Behavior_Promotable_Store_Purchase::update_promotion_items
	 */
	public function test_update_promotion_items()
	{
		$store_purchase = Jam::build('store_purchase');

		$promotion = $this->getMock('Model_Promotion', array('update_store_purchase'), array('promotion'));

		$promotion
			->expects($this->exactly(2))
			->method('update_store_purchase')
			->with($this->identicalTo($store_purchase));

		$behavior = $this->getMock('Jam_Behavior_Promotable_Store_Purchase', array('available_promotions'));

		$behavior
			->expects($this->once())
			->method('available_promotions')
			->will($this->returnValue(array($promotion, $promotion)));

		$behavior->update_promotion_items($store_purchase);
	}
}