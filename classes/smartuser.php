<?php
namespace SmartAuth;

class SmartAuthException extends \FuelException {}
class SmartUserNotFound extends SmartAuthException {}
class SmartUserInvalidCredentials extends SmartAuthException {}

class SmartUser
{
	/**
	 * var  $user  the current logged in user instance
	 */
	protected static $user = null;
	
	protected static $hasher = false;
	
	/**
	 * Initialize class
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
		// if (user_id is set in session): check if user exists
		$user_id = \Session::get('smartuser.user_id');
		if ($user_id)
		{
			// if (user_id is a numeric value): check if user exists
			if (is_numeric($user_id))
			{
				// get user by id
				$user = \Model_User::find($user_id);
				\Log::info('Looking for user', 'SmartUser::perform_check()');

				// if user is found, login
				if ($user !== null)
				{
					static::$user = $user;
					\Log::info('User #'.$user_id.' logged in', 'SmartUser::perform_check()');
					return true;
				}
				// else: throw exception
				else
				{
					\Log::error('User id #'.$user_id.' not found', 'SmartUser::perform_check()');
					static::logout();
					throw new SmartUserNotFound('User id #'.$user_id.' not found');
				}
			}
				// else: throw exception
			else
			{
				\Log::error('User id:'.$user_id.' is invalid', 'SmartUser::perform_check()');
				static::logout();
				throw new SmartUserNotFound('Invalid id: "'.$user_id.'"');
			}
		}
	}
	
	public static function create($user)
	{
		if (\Config::get('smartauth.features.record_last_login.active') == true)
		{
			$user->last_login = null;
		}
		
		// static::set_password($user->password);
		// if (password_salting is active): generate salt then hash password;
		if (\Config::get('smartauth.features.password_salting.active') === true)
		{
			$user->salt = \Str::random('sha1');
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
			\Log::error('Could not create user', 'SmartUser::create_user()');
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
			\Log::error('Could not create user', 'SmartUser::update()');
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

	public static function login($login, $password, $remember_me = false)
	{
		// 1. find if user exists by login
		// 2. if (salting is active): hash password
		// 3. if (user's password = hashed password): login user
				
		// get user by login
		$user = \Model_User::find()->where(\Config::get('smartauth.user.login'), $login)->get_one();
		
		// if (no user is found with 'login'): throw exception
		if ($user === null)
		{
			\Log::info('Cannot find user with '.\Config::get('smartauth.user.login').' = '.$login, 'SmartUser::login()');
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
			\Log::info('User tried to login using invalid credentials', 'SmartUser::login()');
			throw new SmartUserInvalidCredentials(\Lang::get('smartauth.messages.invalid_credentials'));
		}
		
		// login user
		static::login_user($user);
		return true;
	}
	
	protected static function login_user($user)
	{
		// set user id in session
		\Session::set('smartuser.user_id', $user->id);
		\Log::debug('Setting user_id in session', 'SmartUser::login_user()');
		
		// if (cookie_login is active): save hash in cookie
		if (\Config::get('smartauth.features.cookie_login.active'))
		{
			$key = substr(md5(uniqid(rand().\Cookie::get(\Config::get('smartauth.features.cookie_login.cookie_name')))), 0, 16);
			\Cookie::set(\Config::get('smartauth.features.cookie_login.cookie_name'), $key, \Config::get('smartauth.features.cookie_login.expiration'));
		}

		// update last_login
		$user->last_login = \Date::forge()->get_timestamp();
		$user->save();
	}
	
	public static function get($field = null)
	{
		if ($field == null)
		{
			return static::$user;
		}
		else if (! isset(static::$user->{$field}))
		{
			throw new SmartUserException('User field '.$field.' not found!');
		}
		
		return static::$user->{$field};
	}

	public static function change_password($user_id, $password)
	{

		// get user by id
		$user = \Model_User::find($user_id);
		
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
	
	public static function logout()
	{
		// delete session
		\Session::delete('smartuser', true);
		static::$user = null;
	}

	public static function reset_password($login, $new_password, $old_password = null)
	{
		
	}

	/**
	 * Default hash method
	 *
	 * @param   string
	 * @return  string
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
