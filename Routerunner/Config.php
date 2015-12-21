<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 14:52
 */

namespace Routerunner;

class Config
{
	public static $defaults = array(
		// Application
		'mode' => 'production',
		'root' => 'standard',
		// Version
		'scaffold.version' => null,
		// Debugging
		'debug' => true,
		// Logging
		'log.writer' => '\Routerunner\Log',
		'log.level' => \Slim\Log::DEBUG,
		'log.enabled' => true,
		// View
		'scaffold' => 'scaffold',
		// Site
		'SITE' => '',
		'SITENAME' => '',
		'SITEROOT' => '',
		'BASE' => '',
		'ROUTERUNNER_ROOT' => 'RouterunnerCMS/',
		'ROUTERUNNER_BASE' => 'RouterunnerCMS/',
		// Database
		'DB_HOST' => '',
		'DB_NAME' => '',
		'DB_USER' => '',
		'DB_PASS' => '',
		'DB_PREFIX' => '_',
		'PREFIX' => '_',
		'DB_CHARSET' => 'utf8',
		'LANG' => 'en',
		// Mail
		'Mail.Host' => '',
		'Mail.SMTPAuth' => true,
		'Mail.Port' => 465,
		'Mail.Username' => '',
		'Mail.Password' => '',
		'Mail.SMTPSecure' => 'tls',
		'Mail.From' => '',
		'Mail.FromName' => '',
		'Mail.WordWrap' => 50,
		'Mail.CharSet' => 'UTF-8',
		'Mail.Subject' => '',
		// User
		'User.UserFlashVar' => 'member',
		'User.UserArrayToTranslate' => array('nev'=>'name'),
		'User.RememberMeVar' => 'rememberme',
		'User.DefaultGroup' => 1,
		'User.DefaultUniqueScope' => null,
		'User.DefaultUniqueAuth' => null,
		'User.TokenSet' => array('email', 'HTTP_USER_AGENT'),
		'User.TokenExpire' => null,
		'User.TokenUserData' => array('REMOTE_ADDR', 'HTTP_USER_AGENT', 'HTTP_REFERER'),
		'User.TokenSession' => 'token',
		'User.TokenCookie' => 'cookie',
		'User.TokenCookieExpire' => 7776000, // = 90*24*60*60
	);

	public static function custom_config($custom) {
		self::$defaults = array_merge(self::$defaults, $custom);
	}
}
if (isset($_SESSION["runner_config"]) && is_array($_SESSION["runner_config"])) {
	\Routerunner\Config::custom_config($_SESSION["runner_config"]);
}
