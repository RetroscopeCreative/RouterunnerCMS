<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.01.20.
 * Time: 13:01
 */

namespace Routerunner;

class User
{
	private static $me=false;
	private static $email=null;
	private static $name=null;
	private static $custom=array();

	private static $group=null;
	private static $scope=false;
	private static $auth=false;

	private static $session=false;
	private static $tmp=array();
	private static $tmp_token=false;

	private static $token=false;
	private static $token_id=false;
	private static $expire=false;

	private static $alias=0;

	public static function initialize()
	{
		self::token();

		if (!self::$token || !self::$token_id || self::$expire < time()) {
			$flash_var = ((\runner::config('User.UserFlashVar'))
				? \runner::config('User.UserFlashVar') : 'UserFlashVar');
			$fn = "flash";

			$flash = false;
			if (isset($_SESSION['routerunner-logout-' . $flash_var])
				&& $_SESSION['routerunner-logout-' . $flash_var] === true) {
				self::set();
			} elseif (\runner::now($flash_var)) {
				$flash = \runner::now($flash_var);
				$fn = "now";
			} elseif (\runner::stack($flash_var)) {
				$flash = \runner::stack($flash_var);
				$fn = "stack";
			} else {
				$flash = \runner::flash($flash_var);
			}
			if ($flash) {
				self::set($flash);
				\runner::$fn($flash_var, false);

				$rememberme = ((\runner::config('User.RememberMeVar'))
					? \runner::config('User.RememberMeVar') : 'RememberMe');
				if (isset($flash[$rememberme]) && self::$token
					&& ($flash[$rememberme] === 'on' || $flash[$rememberme] === true
						|| $flash[$rememberme] === 'true' || $flash[$rememberme] === '1')
				) {
					$cookie = ((\runner::config('User.TokenCookie'))
						? \runner::config('User.TokenCookie') : 'TokenCookie');
					$cookie_expire = ((\runner::config('User.TokenCookieExpire'))
						? \runner::config('User.TokenCookieExpire') : 30 * 24 * 60 * 60);
					$expire = time() + $cookie_expire;
					$domain = \runner::config('User.TokenCookieDomain');

					setcookie($cookie, self::$tmp_token, $expire, '/', $domain);
					//\Routerunner\Routerunner::$slim->setCookie($cookie, self::$tmp_token, $expire, '/', $domain);
				}
			}
		}
	}

	public static function me(& $email=null, & $name=null, & $group=null, & $custom=array(), & $scope=null, & $auth=null, & $alias=0)
	{
		$return = false;
		if (self::$me && self::token()) {
			$return = self::$me;
			$email = self::$email;
			$name = self::$name;
			$group = self::$group;
			$custom = self::$custom;
			$scope = self::$scope;
			$auth = json_decode(self::$auth, true);
			if (!$auth) {
				$auth = self::$auth;
			}
			$alias = self::$alias;
		}
		return $return;
	}

	public static function get($name)
	{
		$return = false;
		if (isset(self::$$name)) {
			$return = self::$$name;
		} elseif (isset(self::$custom[$name])) {
			$return = self::$custom[$name];
		}
		return $return;
	}

	public static function auth($main, & $sub=array())
	{
		$return = false;
		$auth = json_decode(self::$auth, true);
		if ($main === "user" && ($sub === "profile" || $sub === "logout") && self::$me) {
			$return = true;
		} elseif ($auth && is_array($auth) && isset($auth[$main])) {
			if ($sub && is_array($sub)) {
				$return = array_intersect($sub, $auth[$main]);
			} elseif ($sub && is_string($sub)) {
				$return = (($auth[$main] === "*") ||
					((is_array($auth[$main]) && in_array($sub, $auth[$main]))
						|| (is_string($auth[$main]) && $auth[$main] === $sub)) ? true : false);
			} else {
				$return = (($auth[$main] === "*" || ($sub && $auth[$main] === $sub)) ? true : false);
			}
			$sub = $auth[$main];
		} elseif ($auth && is_array($auth) && !isset($auth[$main])) {
			$sub = array();
		} else {
			$auth = self::$auth;
			$return = ($auth === "*" || ($sub && is_string($sub) && $auth === $sub)) ? true : false;
		}
		return $return;
	}

	public static function logout()
	{
		self::set(null, true);
	}

	private static function set($user=null, $logout=false)
	{
		if (is_null($user)) { // logout user
			self::$me = false;
			self::$email = null;
			self::$name = null;
			self::$group = null;
			self::$scope = null;
			self::$auth = null;
			self::$custom = array();
			self::$alias = 0;
			self::close_token(self::$token);
			$flash_var = ((\runner::config('User.UserFlashVar'))
				? \runner::config('User.UserFlashVar') : 'UserFlashVar');
			if (isset($_SESSION['routerunner-logout-' . $flash_var])
				&& $_SESSION['routerunner-logout-' . $flash_var] === true) {
				$cookie = ((\runner::config('User.TokenCookie'))
					? \runner::config('User.TokenCookie') : 'TokenCookie');
				$domain = \runner::config('User.TokenCookieDomain');
				//\Routerunner\Routerunner::$slim->setCookie($cookie, null, -1, '/', $domain);
				setcookie($cookie, null, -1, '/', $domain);

				$flash_var = ((\runner::config('User.UserFlashVar'))
					? \runner::config('User.UserFlashVar') : 'UserFlashVar');
				\runner::now($flash_var, false);
				\runner::stack($flash_var, false, true);
				\runner::flash($flash_var, false);
				unset($_SESSION['slim.flash'][$flash_var]);
				setcookie($cookie, null, -1, '/');
				unset($_COOKIE[$cookie]);
				unset($_SESSION['routerunner-logout-' . $flash_var]);
			} elseif ($logout) {
				$_SESSION['routerunner-logout-' . $flash_var] = true;

				$cookie = ((\runner::config('User.TokenCookie'))
					? \runner::config('User.TokenCookie') : 'TokenCookie');
				$domain = \runner::config('User.TokenCookieDomain');
				//\Routerunner\Routerunner::$slim->setCookie($cookie, null, -1, '/', $domain);
				setcookie($cookie, null, -1, '/', $domain);

				$flash_var = ((\runner::config('User.UserFlashVar'))
					? \runner::config('User.UserFlashVar') : 'UserFlashVar');
				\runner::now($flash_var, false);
				\runner::stack($flash_var, false, true);
				\runner::flash($flash_var, false);
				unset($_SESSION['slim.flash'][$flash_var]);
				setcookie($cookie, null, -1, '/');
				unset($_COOKIE[$cookie]);
			}
		} elseif (isset($user) && !is_null($user) && is_array($user)) {
			self::set(); // clear user if exists

			$flash_var = ((\runner::config('User.UserFlashVar'))
				? \runner::config('User.UserFlashVar') : 'UserFlashVar');
			if (\runner::now($flash_var)) {
				$flash = \runner::now($flash_var);
			} elseif (\runner::stack($flash_var)) {
				$flash = \runner::stack($flash_var);
			} else {
				$flash = \runner::flash($flash_var);
			}

			$array_to_translate = ((\runner::config('User.UserArrayToTranslate'))
				? \runner::config('User.UserArrayToTranslate') : array());

			if ($flash === $user) {
				foreach ($user as $key => $value) {
					$var = (isset($array_to_translate[$key])) ? $array_to_translate[$key] : $key;
					if ($var === 'email' || $var === 'name' || $var === 'alias') {
						self::$$var = $value;
					} else {
						self::$custom[$var] = $value;
					}
				}

				if (self::get_user()) {
					self::open_token();
					self::set_session_token();
				}
			}
		}
	}

	private static function token()
	{
		if (!self::$token && self::get_session_token()) {
			self::renew_token(self::$token);
		} elseif (!self::$token && self::get_cookie_token()) {
			self::renew_token(self::$token, true);
		}
		return self::$token;
	}

	private static function get_user()
	{
		$SQL = <<<SQL
SELECT u.user_id, u.name, u.usergroup, COALESCE(u.unique_scope, g.scope) AS scope,
COALESCE(u.unique_auth, g.auth) AS auth, u.custom_data, u.alias
FROM `{PREFIX}user` AS u
LEFT JOIN `{PREFIX}usergroup` AS g ON g.usergroup_id = u.usergroup
WHERE u.email = :email AND u.alias = :alias
SQL;
		if (isset(self::$email) && !is_null(self::$email)) {
			$params = array(':email'=>self::$email, ':alias'=>self::$alias);
			if ($result = \db::query($SQL, $params)) {
				$result = array_shift($result);

				self::$name = $result['name'];
				self::$group = $result['usergroup'];
				self::$scope = $result['scope'];
				self::$auth = $result['auth'];
				if (json_decode($result['custom_data'], true)) {
					self::$custom = json_decode($result['custom_data'], true);
				}
				self::$me = $result['user_id'];
				self::$alias = $result['alias'];
			} else {
				$SQL_insert = <<<SQL
INSERT INTO `{PREFIX}user` (email, name, usergroup, unique_scope, unique_auth, custom_data, alias)
VALUES (:email, :name, :usergroup, :unique_scope, :unique_auth, :custom_data, :alias)
SQL;
				if ((!isset(self::$group) || is_null(self::$group)) && \runner::config('User.DefaultGroup')) {
					if (isset(self::$custom['group']) || isset(self::$custom['usergroup'])) {
						self::$group = ((isset(self::$custom['group']))
							? self::$custom['group'] : self::$custom['usergroup']);
					} else {
						self::$group = \runner::config('User.DefaultGroup');
					}
				}
				if ((!isset(self::$scope) || is_null(self::$scope)) && \runner::config('User.DefaultUniqueScope')) {
					self::$scope = \runner::config('User.DefaultUniqueScope');
				}
				if ((!isset(self::$auth) || is_null(self::$auth)) && \runner::config('User.DefaultUniqueAuth')) {
					self::$auth = \runner::config('User.DefaultUniqueAuth');
				}
				$custom = (is_array(self::$custom) && count(self::$custom)) ? json_encode(self::$custom) : null;
				$params_insert = array(
					':email' => self::$email,
					':name' => self::$name,
					':usergroup' => self::$group,
					':unique_scope' => self::$scope,
					':unique_auth' => self::$auth,
					':custom_data' => $custom,
					':alias' => self::$alias,
				);
				self::$me = \db::insert($SQL_insert, $params_insert);
			}
		}
		return self::$me;
	}

	private static function open_token()
	{
		if (self::$me && !self::$token) {
			$SQL = <<<SQL
INSERT INTO {PREFIX}token (`token`, `user`, `open`, `expire`, `user_data`)
VALUES (:token, :user, :open, :expire, :user_data)
SQL;
			$expire = ((\runner::config('User.TokenExpire')) ? \runner::config('User.TokenExpire') : 3600);
			$token = self::create_token();
			self::$expire = time() + $expire;
			$params = array(
				':token' => $token,
				':user' => self::$me,
				':open' => time(),
				':expire' => self::$expire,
				':user_data' => json_encode(self::get_user_data()),
			);
			$token_id = \db::insert($SQL, $params);
			self::$token = $token;
			self::$token_id = dechex($token_id);
		}
	}

	private static function token_expire($token, $renew=false, $open=false)
	{
		$expire_add = ((\runner::config('User.TokenExpire'))
			? \runner::config('User.TokenExpire') : 3600);
		if (self::$me && self::$token && $token === self::$token && self::$expire - time() < ($expire_add / 2)) {
			$SQL = 'UPDATE {PREFIX}token SET expire = :expire ';
			if ($open) {
				$SQL .= ', open = :open ';
			}
			$SQL .= 'WHERE token = :token AND user = :me';

			$expire = time()-1;
			if ($renew) {
				$expire = time()+$expire_add;
			}

			$params = array(
				':expire'=>$expire,
				':token'=>$token,
				':me'=>self::$me,
			);
			if ($open) {
				$params[':open'] = time();
			}
			db::query($SQL, $params);
			self::$expire = $expire;
		}
	}

	private static function close_token($token)
	{
		self::token_expire($token);
		if (!self::$session) {
			self::$session = \runner::config('User.TokenSession');
		}
		if (isset($_SESSION[self::$session])) {
			unset($_SESSION[self::$session]);
		}
	}

	private static function renew_token($token, $open=false)
	{
		self::token_expire($token, true, $open);
	}

	private static function get_user_data()
	{
		$return = null;
		if (\runner::config('User.TokenUserData')) {
			$data = \runner::config('User.TokenUserData');
			foreach ($data as $var) {
				$return[$var] = self::get_variable($var);
			}
		}
		return $return;
	}

	private static function create_token($uid=false, $open=false, $user=false) {
		$token = false;
		if (self::$me || $user) {
			$uid = (($uid !== false) ? $uid : str_replace('.', '-', uniqid('', true)));
			$open = (($open !== false) ? $open : $_SERVER['REQUEST_TIME']);

			if (!self::$me && $user) {
				$SQL = <<<SQL
SELECT u.user_id, u.email, u.name, u.usergroup, COALESCE(u.unique_scope, g.scope) AS scope,
COALESCE(u.unique_auth, g.auth) AS auth, u.custom_data FROM `{PREFIX}user` AS u
LEFT JOIN `{PREFIX}usergroup` AS g ON g.usergroup_id = u.usergroup
WHERE u.user_id = :id
SQL;
				if ($result = \db::query($SQL, array(':id'=>$user))) {
					self::$tmp = array_shift($result);
					$custom = json_decode(self::$tmp['custom_data'], true);
					unset(self::$tmp['custom_data']);
					if (is_array($custom)) {
						self::$tmp = array_merge($custom, self::$tmp);
					}
				}
			}

			$user = (($user !== false) ? $user : self::$me);
			$data = __NAMESPACE__;
			$data .= $open;
			self::$tmp_token = $uid . '-' . $open . '-' . dechex($user);

			$set = ((\runner::config('User.TokenSet'))
				? \runner::config('User.TokenSet')
				: array('email', 'name', 'me', 'HTTP_USER_AGENT', 'LOCAL_ADDR', 'LOCAL_PORT', 'REMOTE_ADDR', 'REMOTE_PORT'));


			foreach ($set as $var) {
				$data .= self::get_variable($var);
			}

			$hash = hash('ripemd128', $uid . $user . md5($data));
			$token = $uid . '-' . $open . '-' . substr($hash, 0, 8) . '-' . substr($hash, 8, 4) . '-' .
				substr($hash, 12, 4) . '-' . substr($hash, 16, 4) . '-' . substr($hash, 20, 12);
		}
		return $token;
	}

	private static function get_cookie_token()
	{
		$token = false;
		$cookie = ((\runner::config('User.TokenCookie'))
			? \runner::config('User.TokenCookie') : 'TokenCookie');

		$tmp_token = (isset($_COOKIE[$cookie]) ? $_COOKIE[$cookie] : '');
		//$tmp_token = \Routerunner\Routerunner::$slim->getCookie($cookie);

		$tmp = explode('-', $tmp_token);
		if (is_array($tmp) && count($tmp) === 4) {
			$uid = $tmp[0].'-'.$tmp[1];
			$open = $tmp[2];
			$user = hexdec($tmp[3]);

			$token = self::create_token($uid, $open, $user);

			$SQL = <<<SQL
SELECT token.token_id, u.user_id, u.email, token.expire FROM `{PREFIX}token` AS token
LEFT JOIN `{PREFIX}user` AS u ON u.user_id = token.user
WHERE token.token = :token
ORDER BY open DESC
LIMIT 1
SQL;

			$params = array(
				':token' => $token,
			);
			if ($result = \db::query($SQL, $params)) {
				self::$me = $result[0]['user_id'];
				self::$email = $result[0]['email'];

				if ($return = self::get_user()) {
					self::$token_id = dechex($result[0]['token_id']);
					self::$token = $token;
					//self::$expire = $result[0]['expire'];
					self::$expire = time() - 1;

					self::set_session_token();
				}
			}
		}
		return $token;
	}

	private static function get_session_token()
	{
		$return = false;
		if (!self::$session) {
			self::$session = \runner::config('User.TokenSession');
		}
		if (isset($_SESSION[self::$session])) {
			$token_data = $_SESSION[self::$session];
			$token_id = hexdec(substr($token_data, 0, strpos($token_data, '-')));
			$token = substr($token_data, strpos($token_data, '-')+1);

			$SQL = <<<SQL
SELECT u.user_id, u.email, u.alias, token.expire FROM `{PREFIX}token` AS token
LEFT JOIN `{PREFIX}user` AS u ON u.user_id = token.user
WHERE token.token_id = :id AND token.token = :token AND token.expire > :expire
ORDER BY open DESC
LIMIT 1
SQL;

			$params = array(
				':id' => $token_id,
				':token' => $token,
				':expire' => time(),
			);
			if ($result = \db::query($SQL, $params)) {
				self::$me = $result[0]['user_id'];
				self::$email = $result[0]['email'];
				self::$alias = $result[0]['alias'];
				self::$expire = $result[0]['expire'];

				if ($return = self::get_user()) {
					self::$token_id = $token_id;
					self::$token = $token;
				}
			}
		}
		return $return;
	}

	private static function set_session_token()
	{
		if (!self::$session) {
			self::$session = \runner::config('User.TokenSession');
		}
		if (self::$token && self::$token_id) {
			$_SESSION[self::$session] = self::$token_id . '-' . self::$token;
		}
	}

	private static function get_variable($var) {
		$return = '';
		if (isset(self::$$var)) {
			$return = self::$$var;
		} elseif (isset(self::$custom[$var])) {
			$return = self::$custom[$var];
		} elseif (isset(self::$tmp[$var])) {
			$return = self::$tmp[$var];
		} elseif (isset($_SERVER[$var])) {
			$return = $_SERVER[$var];
		} elseif (isset($_ENV[$var])) {
			$return = $_ENV[$var];
		} elseif (isset($_SESSION[$var])) {
			$return = $_SESSION[$var];
		} elseif (isset($_COOKIE[$var])) {
			$return = $_COOKIE[$var];
		} elseif (isset($_REQUEST[$var])) {
			$return = $_REQUEST[$var];
		}

		return $return;
	}
}
