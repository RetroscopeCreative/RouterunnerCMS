<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 14:19
 */

namespace Routerunner;

if (session_id() == '') {
	session_start();
}

class Routerunner
{
	/**
	 * @const string
	 */
	const VERSION = '1.1.0';

	/**
	 * predefined vars
	 */
	const BY_TREE = 'BY_TREE';
	const BY_INDEX = 'BY_INDEX';
	const BY_TREE_DESC = 'BY_TREE_DESC';
	const BY_INDEX_DESC = 'BY_INDEX_DESC';

	/**
	 * @var \Slim\Helper\Set
	 */
	public $container;

	public static $static;

    public $dir = __DIR__;

    public static $instances = array();
	public static $instance_stack = array();
	public static $rids = array();

	public static $context = array();

	public static $slim = null;

	public static $loaded = false;

	//public $uid = false;
	//public $gid = false;

	/********************************************************************************
	 * Instantiation and Configuration
	 *******************************************************************************/

	/**
	 * Constructor
	 * @param  array $arguments Associative array of application settings
	 */
	public function __construct($arguments=null, $function=null)
	{
		if (ini_get('xdebug.max_nesting_level')) {
			ini_set('xdebug.max_nesting_level', 200);
		}

		if (!isset($_SESSION["runner"]) && is_array($arguments)) {
			Routerunner::setRunnerParams($arguments);
		}

		if (!self::$loaded) {
			require 'BaseClasses' . DIRECTORY_SEPARATOR . 'BaseRunner.php';
			require 'BaseClasses' . DIRECTORY_SEPARATOR . 'BaseModel.php';
			require 'BaseClasses' . DIRECTORY_SEPARATOR . 'BaseBootstrap.php';
			require 'Tunnel.php';

			require 'Slim' . DIRECTORY_SEPARATOR . 'Slim.php';
			require 'Slim' . DIRECTORY_SEPARATOR . 'RunnerSlim.php';
			\Slim\RunnerSlim::registerAutoloader();
			require 'SlimView.php';

			require_once 'phpquery' . DIRECTORY_SEPARATOR . 'phpQuery.php';
		}
		self::$loaded = true;

		if (isset($arguments) && is_array($arguments)) {
			$this->container['settings'] = array_merge(static::getDefaultSettings(), $arguments);
		} else {
			$this->container['settings'] = static::getDefaultSettings();
		}
		if (isset($_SESSION["routerunner-config"])) {
			$this->container['settings'] = array_merge($_SESSION["routerunner-config"], $this->container['settings']);
		}
		$site_root = rtrim((isset($_SERVER["DOCUMENT_ROOT"]) ? $_SERVER["DOCUMENT_ROOT"] : $this->settings['DOCUMENT_ROOT']), '/\\') . DIRECTORY_SEPARATOR
			. $this->settings['SITEROOT'];
		if (substr($site_root, -1) !== DIRECTORY_SEPARATOR) {
			$site_root .= DIRECTORY_SEPARATOR;
		}
		$this->container['settings']['SITEROOT'] = $site_root;

		if (!function_exists("backend_mode")) {
			require $site_root . 'runner-config.php';
		}

		Routerunner::$static = $this;

		\Routerunner\Db::initialize($this->settings);

		new \Routerunner\Helper($this);

		\Routerunner\Routerunner::$slim = new \Slim\RunnerSlim(array(
			'view' => new \Routerunner\CustomView(),
			'templates.path' => \Routerunner\Helper::$scaffold_class,
		));
		\Routerunner\Routerunner::$slim->notFound(function () {
			return false;
		});

		$method = "get";
		$resource = "/";
		if (!isset($arguments["bootstrap"]) || $arguments["bootstrap"] !== false) {
			\Routerunner\Bootstrap::initialize($this->settings, false);
			$method = \Routerunner\Bootstrap::getMethod();
			$resource = \Routerunner\Bootstrap::getResource();
		}
		if (isset($arguments["method"])) {
			$method = $arguments["method"];
		}
		if (isset($arguments["resource"])) {
			$resource = $arguments["resource"];
		}
		if ($method == "head") {
			$method = "get";
		}
		$routerunner_object = $this;

		\Routerunner\Config::custom_config($this->container['settings']);
		\runner::config("notFound", false);

		if (\Routerunner\Bootstrap::$component) {
			return false;
			exit();
		}

		if (isset($arguments) && isset($function) && !is_string($function) && is_callable($function)
			&& \Routerunner\Routerunner::$slim->now('redirect_url')) {
			$arguments["skip_redirect"] = true;
			$arguments["skip_route"] = true;

			\Routerunner\Routerunner::$slim->$method($resource, function () use ($routerunner_object, $arguments) {
				$this->middleware($routerunner_object, $arguments);
			}, $function, function () {
				if (\Routerunner\Routerunner::$slim->now('redirect_url')) {
					\Routerunner\Routerunner::$slim->redirect(\Routerunner\Routerunner::$slim->now('redirect_url'));
				}
			});

			\Routerunner\Routerunner::$slim->run();
		} elseif (isset($arguments) && isset($function) && !is_string($function) && is_callable($function)) {
			$arguments["skip_redirect"] = true;
			$arguments["skip_route"] = true;

			\Routerunner\Routerunner::$slim->$method($resource, function () use ($routerunner_object, $arguments) {
				$this->middleware($routerunner_object, $arguments);
			}, $function);
			\Routerunner\Routerunner::$slim->notFound(function () use ($routerunner_object, $arguments) {
				\runner::config("notFound", true);
				$this->middleware($routerunner_object, $arguments);
			});

			\Routerunner\Routerunner::$slim->run();
		} elseif (isset($arguments) && is_array($arguments) && isset($arguments['root'])) {
			\Routerunner\Routerunner::$slim->$method($resource, function() use ($routerunner_object, $arguments) {
				$this->middleware($routerunner_object, $arguments);
			});
			\Routerunner\Routerunner::$slim->notFound(function () use ($routerunner_object, $arguments) {
				\runner::config("notFound", true);
				$this->middleware($routerunner_object, $arguments);
			});
			\Routerunner\Routerunner::$slim->run();
		} elseif (!is_string($arguments) && is_callable($arguments)) {
			$function = $arguments;
			$arguments = array("skip_redirect" => true, "skip_route" => true);
			\Routerunner\Routerunner::$slim->$method($resource, function() use ($routerunner_object, $arguments) {
				$this->middleware($routerunner_object, $arguments);
			}, $function, function() {
				if (\Routerunner\Routerunner::$slim->now('redirect_url')) {
					$this->redirect(\Routerunner\Routerunner::$slim->now('redirect_url'));
				}
			});
			\Routerunner\Routerunner::$slim->notFound(function () use ($routerunner_object, $arguments) {
				\runner::config("notFound", true);
				$this->middleware($routerunner_object, $arguments);
			});
			\Routerunner\Routerunner::$slim->run();
		} else {
			$arguments = array("skip_redirect" => true, "skip_route" => true);
			\Routerunner\Routerunner::$slim->$method($resource, function() use ($routerunner_object, $arguments) {
				$this->middleware($routerunner_object, $arguments);
			});
			\Routerunner\Routerunner::$slim->notFound(function () use ($routerunner_object, $arguments) {
				\runner::config("notFound", true);
				$this->middleware($routerunner_object, $arguments);
			});
		}
	}

	public function middleware($routerunner_object, $arguments=array()) {
		\Routerunner\Routerunner::$slim->flashKeep();

		\Routerunner\User::initialize();

		if ($uid = \Routerunner\User::me($email, $name, $gid)) {
			/*
			$this->uid = $uid;
			$this->gid = $gid;
			*/
		}


		if (isset($routerunner_object->container['settings']['log.writer'])) {
			$log_class = $routerunner_object->container['settings']['log.writer'];
			$app = \Routerunner\Routerunner::$slim;
			$app->log->setWriter(new $log_class());

			$app->error(function (\Exception $e) use ($app, $log_class) {
				new $log_class($e);
			});

		}

		if (!isset($arguments["bootstrap"]) || $arguments["bootstrap"] !== false) {
			\Routerunner\Bootstrap::initialize($routerunner_object->settings);

			$history = \Routerunner\Routerunner::$slim->flash('history');
			if (!is_array($history))
				$history = array();
			if (count($history) > 20) {
				$history = array_slice($history, -20, 20);
			}
			if (!count($history)
				|| ((count($history)) && $history[count($history)-1] != \Routerunner\Bootstrap::$fullUri)) {
				$history[] = \Routerunner\Bootstrap::$fullUri;
			}
			\Routerunner\Bootstrap::$history = $history;

			\Routerunner\Routerunner::$slim->flash('history', \Routerunner\Bootstrap::$history);
			if (count($history) > 1) {
				\Routerunner\Routerunner::$slim->now('history.back', $history[count($history)-2]);
			}
		}

		if (!isset($arguments["skip_route"]) || !$arguments["skip_redirect"]) {
			\Routerunner\Routerunner::route();
		}

		if (\Routerunner\Routerunner::$slim->now('redirect_url') &&
			(!isset($arguments["skip_redirect"]) || !$arguments["skip_redirect"])) {
			\Routerunner\Routerunner::$slim->redirect(\Routerunner\Routerunner::$slim->now('redirect_url'));
		}
	}

	public function __destruct()
	{
		self::$slim->halt(200);
	}

	public function __get($name)
	{
		return (isset($this->container[$name]) ? $this->container[$name] : false);
	}

	public function __set($name, $value)
	{
		$this->container[$name] = $value;
	}

	public function __isset($name)
	{
		return isset($this->container[$name]);
	}

	public function __unset($name)
	{
		unset($this->container[$name]);
	}

	public static function runnerParams($paramName="runner", $override=array())
	{
		$params = $override;
		if (isset($_SESSION[$paramName]) && is_array($_SESSION[$paramName])) {
			$params = array_merge($_SESSION[$paramName], $params);
		}
		if (isset($_POST[$paramName]) && is_array(json_decode(base64_decode($_POST[$paramName]), true))) {
			$params = array_merge(json_decode(base64_decode($_POST[$paramName]), true), $params);
		}
		if (isset($_GET[$paramName]) && is_array(json_decode(base64_decode($_GET[$paramName]), true))) {
			$params = array_merge(json_decode(base64_decode($_GET[$paramName]), true), $params);
		}
		return $params;
	}

	public static function setRunnerParams($params, $paramName="runner")
	{
		$_SESSION[$paramName] = $params;
	}

	/**
	 * Get default application settings
	 * @return array
	 */
	public static function getDefaultSettings()
	{
		return \Routerunner\Config::$defaults;
	}

	/**
	 * Configure Slim Settings
	 *
	 * This method defines application settings and acts as a setter and a getter.
	 *
	 * If only one argument is specified and that argument is a string, the value
	 * of the setting identified by the first argument will be returned, or NULL if
	 * that setting does not exist.
	 *
	 * If only one argument is specified and that argument is an associative array,
	 * the array will be merged into the existing application settings.
	 *
	 * If two arguments are provided, the first argument is the name of the setting
	 * to be created or updated, and the second argument is the setting value.
	 *
	 * @param  string|array $name  If a string, the name of the setting to set or retrieve. Else an associated array of setting names and values
	 * @param  mixed        $value If name is a string, the value of the setting identified by $name
	 * @return mixed        The value of a setting if only one argument is a string
	 */
	public function config($name, $value = null)
	{
		if (func_num_args() === 1) {
			if (is_array($name)) {
				$this->settings = array_merge($this->settings, $name);
				$_SESSION["routerunner-config"] = $this->settings;
			} else {
				return isset($this->settings[$name]) ? $this->settings[$name] : null;
			}
		} else {
			$settings = $this->settings;
			$settings[$name] = $value;
			$this->settings = $settings;
		}
		$_SESSION["routerunner-config"] = $this->settings;
	}


    public static function route($route=null, & $router=null, $context=array(), $override=null, $root=false, $echo=true)
    {
		$_route = (is_null($route)) ? self::$static->config("root") : $route;
		$router = new \Routerunner\Router($_route, $context, $override, $root);
		if ($echo && is_null($route) && is_object($router) && isset($router->runner->html)) {
			if (\runner::config('silent')) {
				echo $router->runner->html;
			} else {
				echo '<!--Routerunner::Route(' . $router->rid . ')//-->' . PHP_EOL . $router->runner->html;
			}
		} elseif (!$echo) {
			return $router->runner->html;
		} else {
			return '<!--Routerunner::Route('.$router->rid.')//-->'.PHP_EOL;
		}
    }

	public static function form($formname, $runner, $repost_form_after_submit=false)
	{
		$repost = $repost_form_after_submit;
		\Routerunner\Helper::loader($runner, trim($runner->route, '\\') . '.' . $formname . '.input', $output);
		$html = '';
		if (isset($output['form'], $output['input'])) {
			$form = new \Routerunner\Form($runner, $formname, $output, $repost_form_after_submit);
			$html = $form->render($runner);
			if (\runner::stack("form_failed:" . $formname) === true) {
				$repost = true;
				\runner::stack("form_failed:" . $formname, false);
			}
			if ($repost && $repost_form_after_submit == 'submit') {
				$repost_form_after_submit = 'get';
				$form = new \Routerunner\Form($runner, $formname, $output, $repost_form_after_submit);
				$html .= $form->render($runner);
			}
		}
		return $html;
	}

	public static function process(\Routerunner\BaseRunner & $runner, $html_input=false)
	{
		$html = ($html_input === false) ? $runner->html : $html_input;
		while (preg_match('/\<\!\-\-Routerunner::([\w]+)\(([0-9a-f\.]+)\)\/\/\-\-\>/im', $html, $matches)) {
			switch ($matches[1]) {
				case 'Route':
					$sub_html = (isset(self::$rids[$matches[2]], self::$rids[$matches[2]]->runner->html)
						? self::$rids[$matches[2]]->runner->html : '');
					break;
			}
			$html = str_replace($matches[0], $sub_html, $html);
		}
		if ($html_input === false)
			$runner->html = $html;
		else
			return $html;
	}

    public static function setInstance(\Routerunner\Router $router)
    {
		$path = $router->runner->path . $router->route;
		if (isset(self::$instances[$path]) && in_array($path, self::$instance_stack)) {
			$path .= "/" . $router->rid;
		}
		self::$instances[$path] = $router;
		self::$instance_stack[] = $path;
		self::$rids[$router->rid] = $router;
    }

    public static function getInstance($name)
    {
        return ($name && isset(self::$instances[$name])) ? self::$instances[$name] : false;
    }

	public static function getParentInstance($pop=true, $level=1)
	{
		if ($pop)
			$return = array_pop(self::$instance_stack);
		else
			$return = (count(self::$instance_stack) > 0)
				? self::$instance_stack[count(self::$instance_stack)-$level] : false;
		return self::getInstance($return);
	}

	public static function get($name)
	{
		$static = Routerunner::$static;
		if (is_array($name)) {
			$array = $name;
			while ($name = array_shift($array)) {
				if (is_object($static) && isset($static->$name)) {
					$static = $static->$name;
				} elseif (is_array($static) && isset($static[$name])) {
					$static = $static[$name];
				} else {
					// exception: not found
				}
			}
			return $static;
		} elseif (isset($static->$name)) {
			return $static->$name;
		}
		return false;
	}
}
