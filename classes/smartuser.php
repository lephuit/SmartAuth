<?php
namespace SmartAuth;

class SmartAuthException extends \FuelException {}
class SmartUserNotFound extends SmartAuthException {}
class SmartUserInvalidCredentials extends SmartAuthException {}

class SmartUser
{
	/**
	 * protected  $user  the current logged in user instance
	 */
	protected static $user = null;
	
	protected static $hasher = false;
	
	/**
	 * Initialize SmartUser class
	 * 
	 * @return null
	 */
	public static function _init()
	{
		\Log::info('Loaded SmartUser class', 'SmartUser::_init()');
		\Config::load('smartauth', true);
		\Lang::load('smartauth', 'smartauth');
		static::perform_check();
	}

	/**
	 * Get user from session if found
	 * 
	 * @return boolean if session is found return true
	 */
	protected static function perform_check()
	{
		// if (cookie is set): check if user exists and hash is correct
		if (\Config::get('smartauth.features.persistant_login.active') == true)
		{
			$cookie = \Cookie::get(\Config::get('smartauth.features.persistent_login.cookie_name'));
			if ($cookie)
			{
				list($user_id, $hash) = unserialize($cookie);

				if ($user_id)
				{
					// get user by id
					$user = \Model_User::find($user_id);

					// if user is found, login
					if ($user != null)
					{
						if ($user->login_hash === $hash)
						{
							static::$user = $user;
							\Log::info('User #'.$user_id.' logged in', 'SmartUser::perform_check()');
							return true;
						}
					}
				}
			}
		}
		
		// if (user_id is set in session): check if user exists
		$user_id = \Session::get('smartuser.user_id');
		if ($user_id)
		{
			// get user by id
			$user = \Model_User::find($user_id);

			// if user is found, login
			if ($user != null)
			{
				static::$user = $user;
				\Log::info('User #'.$user_id.' logged in', 'SmartUser::perform_check()');
				return true;
			}
		}
		static::logout();
		return false;
	}
	
	public static function create($user)
	{		
		// static::set_password($user->password);
		// if (password_salting is active): generate salt then hash password;
		if (\Config::get('smartauth.features.password_salting.active') === true)
		{
			$user->{\Config::get('smartauth.user.salt')} = \Str::random('sha1');
			$user->{\Config::get('smartauth.user.password')} = static::hash($user->{\Config::get('smartauth.user.password')}, $user->salt);
		}
		// else: hash password without salt
		else
		{
			$user->{\Config::get('smartauth.user.password')} = static::hash($user->{\Config::get('smartauth.user.password')}, \Config::get('smartauth.password_hash'));
		}
		
		if ($user->save())
		{
			\Log::info('User #'.$user->id.' created successfully', 'SmartUser::create()');
			return $user;
		}
		else
		{
			throw new SmartUserCannotCreateUser('Could not create new user');
		}
	}
	
	public static function update($user)
	{
		// if (password_salting is active): generate salt then hash password;
		if (\Config::get('smartauth.features.password_salting.active') === true)
		{
			$user->{\Config::get('smartauth.user.password')} = static::hash($user->{\Config::get('smartauth.user.password')}, $user->salt);
		}
		// else: hash password without salt
		else
		{
			$user->{\Config::get('smartauth.user.password')} = static::hash($user->{\Config::get('smartauth.user.password')}, \Config::get('smartauth.password_hash'));
		}
		
		if ($user->save())
		{
			\Log::info('User #'.$user->id.' updated successfully', 'SmartUser::update_user()');
			return $user;
		}
		else
		{
			throw new SmartUserCannotUpdateUser('Could not update user');
		}
	}

	public static function is_logged_in()
	{
		return ! is_null(static::$user);
	}

	public static function require_login()
	{
		if (static::is_logged_in())
		{
			return true;
		}
		else
		{
			\Session::set('smartuser.redirect_url', \Uri::string());
			\Response::redirect(\Config::get('smartauth.urls.login'));
		}
	}

	public static function login($login, $password, $persist_login = false)
	{
		// 1. find if user exists by login
		// 2. if (salting is active): hash password
		// 3. if (user's password = hashed password): login user
				
		// get user by login
		$user = \Model_User::find()->where(\Config::get('smartauth.user.username'), $login)->get_one();
		
		// if (no user is found with 'login'): throw exception
		if ($user === null)
		{
			throw new SmartUserInvalidCredentials(\Lang::get('smartauth.messages.invalid_credentials'));
		}

		// if (salting is active): hash password with salt
		if (\Config::get('smartauth.features.password_salting.active') === true)
		{
			$password = static::hash($password, $user->{\Config::get('smartauth.user.salt')});
		}
		// else: hash password without salt
		else
		{
			$password = static::hash($password, \Config::get('smartauth.password_hash'));
		}
		
		// if (user password is not identical to hashed password): throw exception
		if ($user->{\Config::get('smartauth.user.password')} !== $password)
		{
			throw new SmartUserInvalidCredentials(\Lang::get('smartauth.messages.invalid_credentials'));
		}
		
		// login user
		return static::login_user($user, $persist_login);
	}
		
	/**
	 * Login the user
	 * 
	 * @param mixed $user the user object
	 * @param boolean $persist_login
	 */
	protected static function login_user($user, $persist_login)
	{
		// set user id in session
		\Session::set('smartuser.user_id', $user->id);
		\Log::debug('Setting user_id in session', 'SmartUser::login_user()');
		
		// if (cookie_login is active): save hash in cookie
		if ($persist_login and \Config::get('smartauth.features.persistant_login.active'))
		{
			static::persist_login($user);
		}

		// update last_login
		if (\Config::get('smartauth.features.record_info.last_login') == true)
		{
			$user->{\Config::get('smartauth.user.last_login')} = \Date::forge()->get_timestamp();
		}

		// update ip_address
		if (\Config::get('smartauth.features.record_info.ip_address') == true)
		{
			$user->{\Config::get('smartauth.user.ip_address')} = \Input::real_ip();
		}

		$user->save();
		return true;
	}
	
	/**
	 * Persist the user login
	 * Create a new login hash, then save it in a cookie and in the database
	 * 
	 * @param type $user_id the user id
	 */
	protected static function persist_login($user_id)
	{
		$login_hash = Str::random('alnum', 128);
		\Cookie::set(
		   \Config::get('smartauth.features.persistent_login.cookie_name'),
		   $login_hash,
		   \Cookie::get('smartauth.features.persistent_login.expiration')
		);
		$user->{\Config::get('smartauth.user.login_hash')} = static::hash($login_hash, '');
	}
	
	/**
	 * Get current user field
	 * 
	 * @param string $field the requested field
	 * @return string the value of the field
	 */
	public static function get($field = null)
	{
		if ($field == null)
		{
			return static::$user;
		}
		else if (! isset(static::$user->{$field}))
		{
			throw new SmartUserException('User field '.$field.' does not exists!');
		}
		
		return static::$user->{$field};
	}

	/**
	 * Change user password
	 * 
	 * @param string  $user      the user
	 * @param string  $password  the new password
	 * @return boolean
	 */
	public static function change_password($user, $password)
	{
		// if (salting is active): hash passwords with salt
		if (\Config::get('smartauth.features.password_salting.active') === true)
		{
			$password = static::hash($password, $user->salt);
		}
		
		$user->{\Config::get('smartauth.user.password')} = $password;
		if ($user->save())
		{
			\Log::info('User password changed successfully', 'SmartUser::change_password()');
			return true;
		}
	}
	
	/**
	 * Logout out user
	 * Delete cookie and session
	 * 
	 * @return null
	 */
	public static function logout()
	{
		// delete session
		\Cookie::delete(\Config::get('smartauth.features.persistant_cookie.cookie_name'));
		\Session::delete('smartuser', true);
		static::$user = null;
		return true;
	}

	/**
	 * Default hash method
	 *
	 * @param   string  the string to be hashed
	 * @param   string  the salt to be used for hashing
	 * @return  string  the resulting hash
	 */
	public static function hash($string, $salt)
	{
		if ( ! static::$hasher and ! class_exists('PHPSecLib\\Crypt_Hash', false))
		{
			import('phpseclib/Crypt/Hash', 'vendor');
		}
		static::$hasher = new \PHPSecLib\Crypt_Hash();
		return base64_encode(static::$hasher->pbkdf2($string, $salt, 10000, 32));
	}

}
