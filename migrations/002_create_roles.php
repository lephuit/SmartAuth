<?php

namespace Fuel\Migrations;

\Package::load('smartauth');

class Create_roles
{

	public function up()
	{
		\Config::load('smartauth', true);

		\DBUtil::create_table(\Config::get('smartauth.tables.roles'), array(
			'id'    => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
			'role'  => array('type' => 'varchar', 'constraint' => 255),
		), array('id'));

		\DBUtil::create_table(\Config::get('smartauth.tables.assigned_roles'), array(
			'id'          => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
			'role_id'     => array('type' => 'int', 'constraint' => 11),
			'actor_id'    => array('type' => 'int', 'constraint' => 11),
			'actor_type'  => array('type' => 'varchar', 'constraint' => 1),
			'created_at'  => array('type' => 'int', 'constraint' => 11),
			'updated_at'  => array('type' => 'int', 'constraint' => 11),
		), array('id'));

}

	public function down()
	{
		\Config::load('smartauth', true);

		\DBUtil::drop_table(\Config::get('smartauth.tables.roles'));
		\DBUtil::drop_table(\Config::get('smartauth.tables.assigned_roles'));
	}
}
