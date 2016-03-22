<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.18.
 * Time: 18:30
 */

namespace Routerunner;

//use standard\runner;

class Router
{
	public $rid = false;
	public $route = '';
	public $runner = null;
	public $parent = null;
	public $scaffold_root = false;
	public $scaffold_suffix = '';
	public $cache_route = false;

    public function __construct($route=null, $context=array(), $override=null, $root=false)
    {
		$route = str_replace('/', DIRECTORY_SEPARATOR, $route);
		$this->rid = uniqid('', true);

		if (substr($route, 0, 1) == '~' || substr($route, 0, 2) == '/~') {
			$root = \runner::config("scaffold");
			$route = str_replace('~', '', $route);
			if (strpos($route, '@') !== false) {
				$redirect_to = substr($route, strpos($route, '@')+1);
				if (strpos($redirect_to, '?')) {
					$redirect_to = substr($redirect_to, 0, strpos($redirect_to, '?'));
				}
				\runner::now("redirected-subpage", $redirect_to);
				$route = DIRECTORY_SEPARATOR . trim(substr($route, 0, strpos($route, '@')), DIRECTORY_SEPARATOR);
			}
		}
		if (substr($route, 0, 1) == '@' || substr($route, 0, 2) == '/@') {
			$root = \runner::config("BACKEND_DIR") . DIRECTORY_SEPARATOR . 'scaffold';
			$route = str_replace('@', '', $route);
		}

		$this->parent = \Routerunner\Routerunner::getParentInstance(false);
		if ($root && file_exists(realpath(\runner::config('SITEROOT') . $root))) {
			$this->scaffold_root = realpath(\runner::config('SITEROOT') . $root);
		} elseif (strpos($route, \Routerunner\Helper::$scaffold_class) !== false) {
			$this->scaffold_suffix = DIRECTORY_SEPARATOR . substr($route, 0, strpos($route, \Routerunner\Helper::$scaffold_class) + strlen(\Routerunner\Helper::$scaffold_class));
			$route = substr($route, strpos($route, \Routerunner\Helper::$scaffold_class) + strlen(\Routerunner\Helper::$scaffold_class) + 1);
		} elseif (isset($this->parent->scaffold_root) && $this->parent->scaffold_root) {
			$this->scaffold_root = $this->parent->scaffold_root;
		} elseif (isset($this->parent->scaffold_suffix) && $this->parent->scaffold_suffix) {
			$this->scaffold_suffix = $this->parent->scaffold_suffix;
		}
		if ($this->scaffold_suffix) {
			$this->scaffold_root = realpath(\runner::config('SITEROOT') . \runner::config('scaffold')
				. $this->scaffold_suffix);
		}
		if (!$this->scaffold_root) {
			$this->scaffold_root = realpath(\runner::config('SITEROOT') . \runner::config('scaffold'));
		}

		if (substr($route, 0, 1) === DIRECTORY_SEPARATOR) {
			$this->route = $route;
		} elseif ($this->parent) {
			$this->route = $this->parent->runner->path . $this->parent->runner->route . DIRECTORY_SEPARATOR . $route;
		} else {
			$this->route = DIRECTORY_SEPARATOR . $route;
		}

		$this->cache_route = \Routerunner\Bootstrap::$fullUri . '|' . $this->route;

		if (\Routerunner\Helper::includeRoute($this, 'runner', \runner::config("version"))) { // return valid Router with runner included
			if ($override) {
				$this->runner->override = $override;
			}
			if (is_array($context) && count($context)) {
				$this->runner->context = array_merge($this->runner->context, $context);
			}
			$this->runner->files = \Routerunner\Helper::getFiles($this->runner);
			$this->runner->route_parser();
			\Routerunner\Routerunner::getParentInstance();

			if ($this->runner->cache_exp >= 0) {
				$this->set_cache();
			}
		} else {
			// exception: route not found
		}
    }

	public function get_route()
	{
		$return = ($this->parent) ? $this->parent->get_route().$this->route : $this->route;
		if ($return && substr($return, 0, 1) !== DIRECTORY_SEPARATOR) {
			$return = DIRECTORY_SEPARATOR . $return;
		}
		return $return;
	}

	public function get_cache(& $model=false)
	{
		if (\Routerunner\Routerunner::$cache &&
			($html = \Routerunner\Routerunner::$cache->get($this->cache_route . '|html'))) {
			if ($_model = \Routerunner\Routerunner::$cache->get($this->cache_route . '|model')) {
				$model = $_model;
			}
			return $html;
		}
		return false;
	}

	public function set_cache()
	{
		if (\runner::config('mode') != 'backend' && \Routerunner\Routerunner::$cache) {
			\Routerunner\Routerunner::$cache->set($this->cache_route . '|html',
				$this->runner->html, $this->runner->cache_exp);
			\Routerunner\Routerunner::$cache->set($this->cache_route . '|model',
				$this->runner->model, $this->runner->cache_exp);
		}
	}

	public function __destruct()
	{
	}
}