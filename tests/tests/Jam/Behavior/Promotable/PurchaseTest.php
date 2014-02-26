<?php

/**
 * @group jam.behavior.promotable_purchase
 *
 * @package Functest
 * @author Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Jam_Behavior_Promotable_PurchaseTest extends Testcase_Promotions {

	/**
	 * @covers Jam_Behavior_Promotable_Purchase::model_after_check
	 */
	public function test_model_after_check()
	{
		$purchase = Jam::find('purchase', 1);
		$promo_code = Jam::find('promo_code', 2);

		$this->assertTrue($purchase->check());
		$this->assertNull($purchase->promo_code);

		$purchase->promo_code_text = $promo_code->get('code');

		$this->assertTrue($purchase->check());
		$this->assertEquals($promo_code, $purchase->promo_code);

		$purchase->promo_code_text = NULL;

		$this->assertTrue($purchase->check());
		$this->assertNull($purchase->promo_code);
	}

	/**
	 * @covers Jam_Behavior_Promotable_Purchase::model_after_load
	 */
	public function test_model_after_load()
	{
		$purchase = Jam::find('purchase', 1);
		$promo_code = Jam::find('promo_code', 2);
		$purchase->promo_code = $promo_code;
		$purchase->save();

		$purchase = Jam::find('purchase', 1);
		$this->assertEquals($promo_code->code, $purchase->promo_code_text);
	}
}