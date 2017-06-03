<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 15:24
 */

// view functions
//namespace Routerunner;

class runner
{
	static $stack = array();

	public static function config($name, $setter=null)
	{
		if (!is_null($setter)) {
			\Routerunner\Routerunner::$static->config($name, $setter);
		}
		return \Routerunner\Routerunner::$static->config($name);
	}

	public static function redirect_route($route=null, $root=null, $return=false,
										  $context=array(), & $router=null, & $model=null)
	{
		$override = null;
		$returned = self::route($route, $context, $router, $return, $override, $root);
		$model = $router->runner->model;
		if ($model && is_array($model) && count($model) == 1) {
			$model = array_shift($model);
		}
		return $returned;
	}

	public static function traverse($runner=false, $route=false, $parent=false)
    {
        $children = false;
        if (!$route && $runner) {
            $route = $runner->path . $runner->route;
        }
        if (!$parent && isset($runner->model) && is_object($runner->model)) {
            $parent = $runner->model->reference;
        }
        if ($parent) {
            if ($children = \Routerunner\Bootstrap::children($parent)) {
				$current_route = $children[0]['model_class'];
				$ids = array();
                foreach ($children as $child) {
					$child_route = $child['model_class'];
					if ($current_route != $child_route) {
						\runner::route(\runner::config('SITEROOT') . \runner::config('scaffold') . DIRECTORY_SEPARATOR .
							$route . DIRECTORY_SEPARATOR . $current_route, array("traverse" => $ids, "traverse_order" => $ids));
						$ids = array();
						$current_route = $child_route;
					}
					$ids[] = $child['table_id'];
                }
				if (!empty($ids)) {
					\runner::route(\runner::config('SITEROOT') . \runner::config('scaffold') . DIRECTORY_SEPARATOR .
						$route . DIRECTORY_SEPARATOR . $current_route, array("traverse" => $ids, "traverse_order" => $ids));
				}
            }
        }
        return $children;
    }

	public static function route($route=null, $context=array(), & $router=null,
								 $return=false, & $override=null, $root=false)
	{
		if ($return) {
			return self::return_route($route, $context, $router, $override, $root);
		} else {
			echo \Routerunner\Routerunner::route($route, $router, $context, $override, $root);
		}
	}

	public static function return_route($route=null, $context=array(), & $router=null, & $model=null, $root=null)
	{
		$html = \Routerunner\Routerunner::route($route, $router, $context, $model, $root, false);
		$model = $router->runner->model;
		if ($model && is_array($model) && count($model) == 1) {
			$model = array_shift($model);
		}
		return $html;
	}

	public static function get($name)
	{
		return \Routerunner\Routerunner::get($name);
	}

	public static function flash($name, $value=null, $path=null, $now=false)
	{
		$flash = (!is_null($path)) ? $path : '';
		$flash .= (($flash && substr($flash, -1) !== DIRECTORY_SEPARATOR) ? DIRECTORY_SEPARATOR . $name : $name);
		if (isset(\Routerunner\Routerunner::$slim)) {
			if ($now) {
				return \Routerunner\Routerunner::$slim->now($flash, $value);
			} else {
				return \Routerunner\Routerunner::$slim->flash($flash, $value);
			}
		}
	}

	public static function variable($name, $value=null, $path=null)
	{
		return self::flash($name, $value, $path, true);
	}

	public static function now($name, $value=null, $path=null)
	{
		return self::flash($name, $value, $path, true);
	}

	public static function stack($name, $value=null, $keep=false)
	{
		if (!is_null($value)) {
			self::$stack[$name] = $value;
			if ($keep) {
				self::flash($name, $value, 'stack', false);
				if (!isset($_SESSION["flash"])) {
					$_SESSION["flash"] = array();
				}
				$_SESSION["flash"][$name] = $value;
			}
		} elseif (is_null($value) && isset(self::$stack[$name])) {
			return self::$stack[$name];
		} elseif (is_null($value) && ($value = \runner::flash($name, null, 'stack', false))) {
			return $value;
		} elseif (is_null($value) && isset($_SESSION["flash"][$name])) {
			return $_SESSION["flash"][$name];
		} else {
			return false;
		}
	}

	public static function cache($key, $value=null, $cache_exp=null) {
		if (is_array($key)) {
			$key = implode('|', $key);
		}
		if (is_null($cache_exp)) {
			$cache_exp = 2592000;
		}
		if (is_null($value)) {
			if (\Routerunner\Routerunner::$cache &&
				($value = \Routerunner\Routerunner::$cache->get($key))) {
				return $value;
			}
		} elseif ($value === false) {
			\Routerunner\Routerunner::$cache->delete($key);
		} else {
			if (\Routerunner\Routerunner::$cache && \Routerunner\Routerunner::$cache_type == 'Memcached') {
				\Routerunner\Routerunner::$cache->set($key, $value, $cache_exp);
			} elseif (\Routerunner\Routerunner::$cache && \Routerunner\Routerunner::$cache_type == 'Memcache' &&
				strlen($key) < 250) {
				\Routerunner\Routerunner::$cache->set($key, $value, MEMCACHE_COMPRESSED, $cache_exp);
			}
		}
	}

	public static function redirect($url)
	{
		\Routerunner\Routerunner::$slim->now('redirect_url', $url);
	}

	public static function toAscii($str, $replace=array(), $delimiter='-') {
		if( !empty($replace) ) {
			$str = str_replace((array)$replace, ' ', $str);
		}

		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -\.]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -,!?.]+/", $delimiter, $clean);
		$clean = trim(preg_replace("/[--]+/", "-", $clean), "-");

		return $clean;
	}

	public static function get_rewrite_url($url, $resource_uri=null, $reference=null) {
		$url = self::toAscii($url);

		$SQL = 'SELECT rewrite_id FROM `{PREFIX}rewrites` WHERE url = :url ';
		$params = array(
			":url" => $url,
		);
		if (!is_null($resource_uri)) {
			$SQL .= ' AND resource_uri <> :resource_uri ';
			$params[":resource_uri"] = $resource_uri;
		}
		if (!is_null($reference)) {
			$SQL .= ' AND reference <> :reference ';
			$params[":reference"] = $reference;
		}

		if (\db::query($SQL, $params)) {
			$url .= "-" . strftime("%Y%m%d-%H%M%S", time());
		}
		return $url;
	}

	public static function stack_js($add_js='') {
		\runner::now("javascript_after", \runner::now("javascript_after") . $add_js);
	}
	public static function js_after() {
		$return = '';
		if ($jscript = \runner::now("javascript_after")) {
			$return = <<<JSCRIPT
<script language='javascript'>
{$jscript}
</script>
JSCRIPT;
		}
		return $return;
	}
	public static function js_after_fn() {
		$return = '';
		if ($jscript = \runner::now("javascript_after")) {
			$return = <<<JSCRIPT
function() {
{$jscript}
}
JSCRIPT;
		}
		return $return;
	}
	public static function plugin($script, $runner, $callback='') {
		echo $runner->plugins($script, $callback);
	}
}

setlocale(LC_ALL, 'en_US.UTF8');
