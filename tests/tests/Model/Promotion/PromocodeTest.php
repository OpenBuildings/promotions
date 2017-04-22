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
	 * @covers Model_Promotion_Promocode::matches_brand_purchase_promo_code
	 */
	public function test_matches_brand_purchase_promo_code()
	{
		$brand_purchase = Jam::build('brand_purchase', array('purchase' => Jam::build('purchase')));

		$promotion = $this->getMockBuilder('Model_Promotion_Promocode')
			->setMethods(array('has_promo_code'))
			->setConstructorArgs(array('promotion_promocode'))
			->getMock();
		$promo_code = Jam::build('promo_code');

		$promotion
			->expects($this->exactly(2))
			->method('has_promo_code')
			->with($this->identicalTo($promo_code))
			->will($this->onConsecutiveCalls(TRUE, FALSE));

		$this->assertFalse($promotion->matches_brand_purchase_promo_code($brand_purchase));

		$brand_purchase->purchase->promo_code = $promo_code;

		$this->assertTrue($promotion->matches_brand_purchase_promo_code($brand_purchase));
		$this->assertFalse($promotion->matches_brand_purchase_promo_code($brand_purchase));
	}

	/**
	 * @covers Model_Promotion_Promocode::applies_to
	 */
	public function test_applies_to()
	{
		$brand_purchase = Jam::build('brand_purchase');

		$promotion = $this->getMockBuilder('Model_Promotion_Promocode')
			->setMethods(array('matches_brand_purchase_promo_code'))
			->setConstructorArgs(array('promotion'))
			->getMock();

		$promotion
			->expects($this->exactly(2))
			->method('matches_brand_purchase_promo_code')
			->with($this->identicalTo($brand_purchase))
			->will($this->onConsecutiveCalls(TRUE, FALSE));

		$this->assertTrue($promotion->applies_to($brand_purchase));
		$this->assertFalse($promotion->applies_to($brand_purchase));
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
