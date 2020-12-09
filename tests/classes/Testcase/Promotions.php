<?php

/**
 * Testcase_Promotions definition
 *
 * @package Functest
 * @author Ivan Kerin
 * @author Yasen Yanev
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Testcase_Promotions extends \PHPUnit\Framework\TestCase {

	public function setUp(): void
	{
		parent::setUp();
		Database::instance()->begin();
		Jam_Association_Creator::current(1);
	}

	public function tearDown(): void
	{
		Database::instance()->rollback();
		parent::tearDown();
	}

	public function ids(array $items)
	{
		return array_values(array_map(function($item){ return $item->id(); }, $items));
	}
}
