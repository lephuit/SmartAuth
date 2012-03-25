<?php

namespace Fuel\Migrations;

\Package::load('smartauth');

class Create_users {

	public function up()
	{
		\Config::load('smartauth', true);
		$table      = \Config::get('smartauth.tables.users');
		$username   = \Config::get('smartauth.user.username');
		$password   = \Config::get('smartauth.user.password');
		$email      = \Config::get('smartauth.user.email');
		
		$fields['id'] = array('constraint' => 11, 'type' => 'int', 'auto_increment' => true);
		$fields[$username]   = array('constraint' => 255, 'type' => 'varchar');
		$fields[$password]   = array('constraint' => 255, 'type' => 'varchar');
		$fields[$email]      = array('constraint' => 255, 'type' => 'varchar');
		if (\Config::get('smartauth.features.password_salting.active') == true)
		{
			$salt       = \Config::get('smartauth.user.salt');
			$fields[$salt]       = array('constraint' => 255, 'type' => 'varchar');
		}
		if (\Config::get('smartauth.features.record_info.last_login') == true)
		{
			$last_login = \Config::get('smartauth.user.last_login');
			$fields[$last_login] = array('constraint' => 11,  'type' => 'int');
		}
		if (\Config::get('smartauth.features.record_info.ip_address') == true)
		{
			$ip_address = \Config::get('smartauth.user.ip_address');
			$fields[$ip_address] = array('constraint' => 20,  'type' => 'varchar');
		}
		if (\Config::get('smartauth.features.persistant_login.active') == true)
		{
			$login_hash = \Config::get('smartauth.user.login_hash');
			$fields[$login_hash] = array('constraint' => 255, 'type' => 'varchar');
		}
		$fields['created_at'] = array('constraint' => 11, 'type' => 'int');
		$fields['updated_at'] = array('constraint' => 11, 'type' => 'int');
			
		\DBUtil::create_table($table, $fields, array('id'));
	}

	public function down()
	{
		\Config::load('smartauth', true);
		\DBUtil::drop_table(\Config::get('smartauth.tables.users'));
	}
}
