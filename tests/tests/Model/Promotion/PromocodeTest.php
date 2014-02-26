<?php

/**
 * @group model.promotion_promocode
 *
 * @package Functest
 * @author Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Model_Promotion_PromocodeTest extends Testcase_Promotions {

	/**
	 * @covers Model_Promotion_Promocode::matches_store_purchase_promo_code
	 */
	public function test_matches_store_purchase_promo_code()
	{
		$store_purchase = Jam::build('store_purchase', array('purchase' => Jam::build('purchase')));

		$promotion = $this->getMock('Model_Promotion_Promocode', array('has_promo_code'), array('promotion_promocode'));
		$promo_code = Jam::build('promo_code');

		$promotion
			->expects($this->exactly(2))
			->method('has_promo_code')
			->with($this->identicalTo($promo_code))
			->will($this->onConsecutiveCalls(TRUE, FALSE));

		$this->assertFalse($promotion->matches_store_purchase_promo_code($store_purchase));

		$store_purchase->purchase->promo_code = $promo_code;

		$this->assertTrue($promotion->matches_store_purchase_promo_code($store_purchase));
		$this->assertFalse($promotion->matches_store_purchase_promo_code($store_purchase));
	}

	/**
	 * @covers Model_Promotion_Promocode::applies_to
	 */
	public function test_applies_to()
	{
		$store_purchase = Jam::build('store_purchase');

		$promotion = $this->getMock('Model_Promotion_Promocode', array('matches_store_purchase_promo_code'), array('promotion'));

		$promotion
			->expects($this->exactly(2))
			->method('matches_store_purchase_promo_code')
			->with($this->identicalTo($store_purchase))
			->will($this->onConsecutiveCalls(TRUE, FALSE));

		$this->assertTrue($promotion->applies_to($store_purchase));
		$this->assertFalse($promotion->applies_to($store_purchase));
	}

	/**
	 * @covers Model_Promotion_Promocode::has_promo_code
	 */
	public function test_has_promo_code()
	{
		$promotion1 = Jam::find('promotion', 1);
		$promotion2 = Jam::find('promotion', 2);
		$promo_code1 = Jam::find('promo_code', 1);
		$promo_code2 = Jam::find('promo_code', 2);
		$promo_code3 = Jam::find('promo_code', 3);

		$this->assertTrue($promotion1->has_promo_code($promo_code1));
		$this->assertFalse($promotion1->has_promo_code($promo_code2));
		$this->assertTrue($promotion2->has_promo_code($promo_code2));
		$this->assertTrue($promotion2->has_promo_code($promo_code3));
	}


}