<?php
namespace SmartAuth;

class SmartGroupException extends SmartAuthException {}
class SmartGroupGroupNotFound extends SmartGroupException {}

class SmartGroup
{
	public static function is_member($user_id, $group_id)
	{
		$user = \Model_User::find($user_id);
		$groups = $user->groups;

		foreach ($groups as $group)
		{
			while ($group)
			{
				if ($group->id == $group_id)
				{
					return true;
				}
				$group = $group->parent;
			}
		}
		return false;
	}
}
