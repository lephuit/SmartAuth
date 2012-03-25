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
	/**
	 * Routes setting
	 */
	'uris' => array(
		// Login URI
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
	/**
	 * Password settings
	 */
	'password' => array(
		// Minimum password length
		'minimum_length' => 6,
		/**
		 * Requires password complexity minimum requirements
		 * 
		 * If a rule is true, the password must include one member of its 
		 * character set.
		 * 
		 * 
		 * 'letters' => either a lowercase or uppercase letter must be
		 *     present in the password
		 * 
		 * 'lowercase',
		 * 'uppercase' => a letter in the stated case must be
		 *    present
		 */
		'complexity' => array(
			'numbers'   => true,
			'letters'  => true,
			'lowercase' => true,
			'uppercase' => true,
			'symbols'   => false,
		),
	),
	'group' => array(
	),
	/**
	 * Features controll
	 */
	'features' => array(
		//* Groups configuration
		'groups' => array(
			// Actiavted groups
			'active' => true,
		),
		/**
		 * Password salting
		 */
		'password_salting' => array(
			'active'     => true,
			// Password salt 
			'salt' => 'CREATE A DIFFERENT SALT FOR EACH PROJECT',
			// Create a different salt per user
			'user_based' => true,
		),
		/**
		 * Record user data
		 */
		'record_info' => array(
			// Record last login time
			'last_login' => true,
			// Record last login IP address
			'ip_address' => false,
		),
		/**
		 * Allow users to use cookies to keep them logged in
		 */
		'persistent_login' => array(
			'active' => true,
			// Cookie expiration time
			'expiration' => 60 * 60 * 24 * 7,
			// Cookie name
			'cookie_name' => 'smartauth_cookie',
		),
		/**
		 * Activate account by email
		 */
		'email_activation' => array(
			'active' => false,
			// Activation hash validity duration
			'expiration' => 60 * 60 * 24 * 3,
		),
		/**
		 * Reset password through email
		 */
		'reset_password'    => array(
			'active' => false,
			// Email hash validity duration
			'expiration' => 60 * 60 * 24,
		),
	),
);
