<?php

/**
 * @group jam.behavior.promotable_purchase
 *
 * @package Functest
 * @author Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Jam_Validator_Rule_Purchase_PromocodeTest extends Testcase_Promotions {

	/**
	 * @covers Jam_Validator_Rule_Purchase_Promocode::valid_promo_code
	 */
	public function test_valid_promo_code()
	{
		$purchase = Jam::find('purchase', 1);
		$promo_code1 = Jam::find('promo_code', 1);
		$purchase->update_fields(array('promo_code_id' => $promo_code1->get('id')));

		$promo_code2 = Jam::find('promo_code', 2);
		$promo_code3 = Jam::find('promo_code', 3);

		$validator_rule = Jam::validator_rule('purchase_promocode');

		// Invalid code
		$result = $validator_rule->valid_promo_code('asd', $purchase);
		$this->assertNull($result);

		// Already used promocode
		$result = $validator_rule->valid_promo_code($promo_code1->get('code'), $purchase);
		$this->assertEquals($promo_code1, $result);

		// Available promocode
		$result = $validator_rule->valid_promo_code($promo_code2->get('code'), $purchase);
		$this->assertEquals($promo_code2, $result);

		// Check already used promocode
		Jam::find('purchase', 2)->set('promo_code', $promo_code2)->save();

		$result = $validator_rule->valid_promo_code($promo_code2->get('code'), $purchase);
		$this->assertNull($result, 'Already used promocode');

		// Check already used allow_multiple
		Jam::find('purchase', 2)->set('promo_code', $promo_code3)->save();

		$result = $validator_rule->valid_promo_code($promo_code3->get('code'), $purchase);
		$this->assertEquals($promo_code3, $result, 'Should match because its an allow_multiple=TRUE promocode');
	}

	/**
	 * @covers Jam_Validator_Rule_Purchase_Promocode::validate
	 */
	public function test_validate()
	{
		$purchase = Jam::find('purchase', 1);
		$purchase2 = Jam::find('purchase', 1);
		$purchase3 = Jam::find('purchase', 1);
		$promo_code = $this->getMockBuilder('Model_Promo_Code')
			->setMethods(array('validate_purchase'))
			->setConstructorArgs(array('promo_code'))
			->getMock();
		$promo_code
			->expects($this->once())
			->method('validate_purchase')
			->with($this->identicalTo($purchase2));

		$expired_promo_code = Jam::build('promo_code', array(
			'expires_at' => date('Y-m-d H:i:s', strtotime('-1 month')),
		));

		$validator_rule = $this->getMockBuilder('Jam_Validator_Rule_Purchase_Promocode')
			->setMethods(array('valid_promo_code'))
			->setConstructorArgs(array(array()))
			->getMock();
		$validator_rule
			->expects($this->exactly(3))
			->method('valid_promo_code')
			->with($this->equalTo('PROMOCODE'), $this->isInstanceOf('Model_Purchase'))
			->will($this->onConsecutiveCalls(NULL, $promo_code, $expired_promo_code));

		$validator_rule->validate($purchase, 'promo_code', 'PROMOCODE');
		$this->assertFalse($purchase->is_valid());

		$errors = $purchase->errors()->as_array();
		$expected = array('promo_code_text' => array('invalid' => array()));
		$this->assertEquals($expected, $errors);

		$validator_rule->validate($purchase2, 'promo_code', 'PROMOCODE');
		$this->assertTrue($purchase2->is_valid());

		$validator_rule->validate($purchase3, 'promo_code', 'PROMOCODE');
		$this->assertFalse($purchase3->is_valid());

		$errors = $purchase3->errors()->as_array();
		$expected = array('promo_code_text' => array('expired' => array()));
		$this->assertEquals($expected, $errors);
	}
}
