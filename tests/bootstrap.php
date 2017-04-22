<?php

require_once __DIR__.'/../vendor/autoload.php';

Kohana::modules(array(
	'database'      => MODPATH.'database',
	'auth'          => MODPATH.'auth',
	'jam'           => MODPATH.'jam',
	'jam-monetary'  => MODPATH.'jam-monetary',
	'jam-auth'      => MODPATH.'jam-auth',
	'shipping'      => MODPATH.'shipping',
	'purchases'     => MODPATH.'purchases',
	'promotions'    => __DIR__.'/..',
));

Kohana::$config
	->load('database')
		->set('default', array(
			'type'       => 'PDO',
			'connection' => array(
				'dsn'        => 'mysql:host=localhost;dbname=OpenBuildings/promotions',
				'username'   => 'root',
				'password'   => '',
				'persistent' => TRUE,
			),
			'table_prefix' => '',
			'identifier'   => '`',
			'charset'      => 'utf8',
			'caching'      => FALSE,
		));

Kohana::$config
	->load('auth')
		->set('session_type', 'Auth_Test')
		->set('session_key', 'auth_user')
		->set('hash_key', '11111');

Kohana::$environment = Kohana::TESTING;
