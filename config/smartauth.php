<?php
/**
 * NOTICE:
 *
 * If you need to make modifications to the default configuration, copy
 * this file to your app/config folder, and make them in there.
 *
 * This will allow you to upgrade fuel without losing your custom config.
 */

return array(
	'urls' => array(
		'login' => 'users\login',
	),
	'user' => array(
		'login'      => 'username',
		'password'   => 'password',
		'salt'       => 'salt',
	),
	'group' => array(
		'accessors'  => array('id','name'),
	),
	'features' => array(
		'groups'            => array('active' => true),
		'password_salting'  => array('active' => true),
		'record_last_login' => array('active' => true),
		'cookie_login'      => array('active' => false, 'expiration' => 60 * 60 * 24 * 7, 'cookie_name' => 'smartauth_cookie'),
		'email_activation'  => array('active' => false, 'expiration' => 60 * 60 * 24 * 3),
		'reset_password'    => array('active' => false, 'expiration' => 60 * 60 * 24 * 1),
	),
	'password_hash' => 'nbd5bgn64vbun6BT5y45V#Cev5rbuT&%4vd3s4@#$VBn7m',
);
