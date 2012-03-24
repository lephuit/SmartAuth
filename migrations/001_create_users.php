<?php

namespace Fuel\Migrations;

\Package::load('smartauth');

class Create_users {

	public function up()
	{
		\Config::load('smartauth', true);
		$login = \Config::get('smartauth.user.login');
		
		\DBUtil::create_table(\Config::get('smartauth.tables.users'), array(
			'id'          => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
			'login'       => array('constraint' => 255, 'type' => 'varchar'),
			'password'    => array('constraint' => 81, 'type' => 'varchar'),
			'salt'        => array('constraint' => 81, 'type' => 'varchar'),
			'email'       => array('constraint' => 255, 'type' => 'varchar'),
			'last_login'  => array('constraint' => 11, 'type' => 'int'),
			'created_at'  => array('constraint' => 11, 'type' => 'int'),
			'updated_at'  => array('constraint' => 11, 'type' => 'int'),
		), array('id'));
	}

	public function down()
	{
		\Config::load('smartauth', true);

		\DBUtil::drop_table(\Config::get('smartauth.tables.users'));
	}
}
