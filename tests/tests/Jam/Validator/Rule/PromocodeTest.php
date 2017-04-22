<?php

/**
 * @group jam.behavior.promotable_purchase
 *
 * @package Functest
 * @author Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Jam_Validator_Rule_PromocodeTest extends Testcase_Promotions {

	/**
	 * @covers Jam_Validator_Rule_Promocode::valid_promo_code
	 */
	public function test_valid_promo_code()
	{
		$purchase = Jam::find('purchase', 1);
		$promo_code1 = Jam::find('promo_code', 1);
		$purchase->update_fields(array('promo_code_id' => $promo_code1->get('id')));

		$promo_code2 = Jam::find('promo_code', 2);
		$promo_code3 = Jam::find('promo_code', 3);

		$validator_rule = Jam::validator_rule('promocode');

		// Invalid code
		$result = $validator_rule->valid_promo_code('asd');
		$this->assertNull($result);

		// Already used promocode
		$result = $validator_rule->valid_promo_code($promo_code1->get('code'));
		$this->assertNull($result);

		// Available promocode
		$result = $validator_rule->valid_promo_code($promo_code2->get('code'));
		$this->assertEquals($promo_code2, $result);

		// Check already used allow_multiple
		Jam::find('purchase', 2)->set('promo_code', $promo_code3)->save();

		$result = $validator_rule->valid_promo_code($promo_code3->get('code'));
		$this->assertEquals($promo_code3, $result, 'Should match because its an allow_multiple=TRUE promocode');
	}

	/**
	 * @covers Jam_Validator_Rule_Promocode::validate
	 */
	public function test_validate()
	{
		$model = Jam::find('product', 1);
		$model2 = Jam::find('product', 1);
		$model3 = Jam::find('product', 1);
		$promo_code = Jam::build('promo_code');
		$expired_promo_code = Jam::build('promo_code', array(
			'expires_at' => date('Y-m-d H:i:s', strtotime('-1 month')),
		));

		$validator_rule = $this->getMockBuilder('Jam_Validator_Rule_Promocode')
			->setMethods(array('valid_promo_code'))
			->setConstructorArgs(array(array()))
			->getMock();
		$validator_rule
			->expects($this->exactly(3))
			->method('valid_promo_code')
			->with($this->equalTo('PROMOCODE'))
			->will($this->onConsecutiveCalls(NULL, $promo_code, $expired_promo_code));

		$validator_rule->validate($model, 'promo_code', 'PROMOCODE');
		$this->assertFalse($model->is_valid());

		$errors = $model->errors()->as_array();
		$expected = array('promo_code_text' => array('invalid' => array()));
		$this->assertEquals($expected, $errors);

		$validator_rule->validate($model2, 'promo_code', 'PROMOCODE');
		$this->assertTrue($model2->is_valid());

		$validator_rule->validate($model3, 'promo_code', 'PROMOCODE');
		$this->assertFalse($model3->is_valid());

		$errors = $model3->errors()->as_array();
		$expected = array('promo_code_text' => array('expired' => array()));
		$this->assertEquals($expected, $errors);
	}
}
