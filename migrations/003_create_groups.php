<?php

namespace Fuel\Migrations;

\Package::load('smartauth');

class Create_groups
{

	public function up()
	{
		\Config::load('smartauth', true);

		\DBUtil::create_table(\Config::get('smartauth.tables.groups'), array(
			'id'        => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
			'name'      => array('type' => 'varchar', 'constraint' => 255),
			'parent_id' => array('type' => 'int', 'constraint' => 11),
		), array('id'));

		\DBUtil::create_table(\Config::get('smartauth.tables.groups_users'), array(
			'id'          => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
			'user_id'     => array('type' => 'int', 'constraint' => 11),
			'group_id'    => array('type' => 'int', 'constraint' => 11),
		), array('id'));

}

	public function down()
	{
		\Config::load('smartauth', true);

		\DBUtil::drop_table(\Config::get('smartauth.tables.groups'));
		\DBUtil::drop_table(\Config::get('smartauth.tables.groups_users'));
	}
}
