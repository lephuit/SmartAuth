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
	'tables' => array(
		'users'       => 'users',
		'roles'       => 'roles',
		'groups'      => 'groups',
		'user_roles'  => 'user_roles',
		'group_roles' => 'group_roles',
	),
	'user' => array(
		'username'   => 'username',
		'email'      => 'email',
		'password'   => 'password',
		'salt'       => 'salt',
		'last_login' => 'last_login',
		'ip_address' => 'ip_address',
		'login_hash' => 'login_hash',
	),
	'group' => array(
		'accessors'  => array('id','name'),
	),
	'features' => array(
		'groups' => array(
			'active' => true,
		),
		'password_salting' => array(
			'active' => true,
		),
		'record_info' => array(
			'last_login' => true,
			'ip_address' => false,
		),
		'persistent_login' => array(
			'active' => true,
			'expiration' => 60 * 60 * 24 * 7,
			'cookie_name' => 'smartauth_cookie',
		),
		'email_activation' => array(
			'active' => false,
			'expiration' => 60 * 60 * 24 * 3,
		),
		'reset_password'    => array(
			'active' => false,
			'expiration' => 60 * 60 * 24,
		),
	),
	'password_hash' => 'nbd5bgn64vbun6BT5y45V#Cev5rbuT&%4vd3s4@#$VBn7m',
);
