<?php
namespace SmartAuth;

class SmartRoleException extends \FuelException {}
class SmartRoleNotFound extends SmartRoleException {}

class SmartRole
{
	
	public static function _init()
	{
		\Log::info('Loaded SmartRole class', 'SmartRole::_init()');
		\Config::load('smartauth', true);
		\Lang::load('smartauth', 'smartauth');
	}
	
	/**
	 * check if current user has role
	 * 
	 * @param string $role role to look for
	 * @param array $actor, has two keys: 'type' and 'id'
	 */
	public static function require_role($role)
	{
		// make sure user is logged in
		SmartUser::require_login();
		if ( ! static::has_role($role, SmartUser::get('id')))
		{
			\Session::set_flash('error', Lang::get('roles.messages.not_authorized', array(), 'You are not authorized to do this action!'));
			\Response::redirect('/');
		}
	}
	/**
	 * Check if a role has right
	 * 
	 * @param int $role the role to look for
	 * @param array $user_id 
	 * @return boolean 
	 */
	public static function has_role($role, $user_id)
	{
		$roles = \Model_Role::get_user_roles($user_id);

		foreach ($roles as $r)
		{
			if ($r->name == $role)
			{
				return true;
			}
		}

		return false;
	}
	
	/**
	 * Create a permission
	 * 
	 * @param array $role the role
	 * @return boolean 
	 */
	public function create_role($role)
	{
		\Model_Role::create($role);
	}

	/**
	 * Grant a user a certain role
	 * 
	 * @param string  $role  the role
	 * @param int     $id    the user id
	 * @return boolean 
	 */
	public static function grant_role_to_user($role, $id)
	{
		
	}
}
