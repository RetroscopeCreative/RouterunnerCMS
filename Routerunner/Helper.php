<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.19.
 * Time: 14:49
 */

namespace Routerunner;

class Helper
{
	public static $document_root = '';
	public static $scaffold_root = '';
	public static $scaffold_class = '';
	public static $static = null;
	public static $model_created = false;

	private static $tmp_elem = null;

	public function __construct(\Routerunner\Routerunner $Routerunner)
	{
		\Routerunner\Helper::$static = $this;

		$Routerunner->container['ROUTERUNNER_ROOT'] =
			substr($Routerunner->dir, 0, strrpos($Routerunner->dir, DIRECTORY_SEPARATOR));
		$Routerunner->container['DOCUMENT_ROOT'] = substr($Routerunner->container['ROUTERUNNER_ROOT'], 0,
			strrpos($Routerunner->container['ROUTERUNNER_ROOT'], DIRECTORY_SEPARATOR));

		\Routerunner\Helper::$document_root = $Routerunner->container['DOCUMENT_ROOT'];
		\Routerunner\Helper::$scaffold_class = $Routerunner->container['settings']['scaffold'];
		\Routerunner\Helper::$scaffold_root = \Routerunner\Helper::$document_root . DIRECTORY_SEPARATOR .
			\Routerunner\Helper::$scaffold_class;
	}

	public static function getFiles(\Routerunner\BaseRunner $runner)
	{
		$current_path = $runner->router->scaffold_root . $runner->path . $runner->route;
		$route_clean = trim($runner->route, DIRECTORY_SEPARATOR);
		$model_path = $runner->router->scaffold_root . DIRECTORY_SEPARATOR . "model" . DIRECTORY_SEPARATOR .
			$route_clean . DIRECTORY_SEPARATOR;
		$files = array(
			'dir' => array(),
			'version' => array(),
		);
		$dir = array();
		if (file_exists($current_path)) {
			$dir = scandir($current_path);
		}
		if (file_exists($model_path) && ($model_dir = scandir($model_path))) {
			foreach ($model_dir as $file) {
				if (!in_array($file, $dir)) {
					$dir[] = $file;
				}
			}
		}
		foreach ($dir as $file) {
			if (is_dir($current_path.DIRECTORY_SEPARATOR.$file) && $file != '.' && $file != '..') {
				$files['dir'][] = $file;
				if (strpos($file, '.') !== false) {
					$file_array = explode('.', $file);
					if (count($file_array) > 1 && $file_array[0] == $route_clean) {
						$version = str_replace($route_clean.'.', '', $file);
						$files['version'][$version] = $file;
					}
				}
			} else {
				$file_array = explode('.', $file);
				if (count($file_array) > 1 && $file_array[count($file_array)-1] == 'php'
					&& $file_array[0] == $route_clean) {

					$file = ((substr($file, -4) === '.php') ? substr($file, 0, -4) . '.' : $file);

					if (isset($files[$file_array[1]])) {
						if (!is_array($files[$file_array[1]]))
							$files[$file_array[1]] = array($files[$file_array[1]]);

						$files[$file_array[1]][] = $file;
					} else {
						$files[$file_array[1]] = $file;
					}
				}
			}
		}

		return $files;
	}

	public static function getDirectory(\Routerunner\BaseRunner $runner, & $files=array())
	{
		$directory = str_replace('\\runner', '', get_class($runner));
		$directory = trim(str_replace('\\', DIRECTORY_SEPARATOR,
			substr($directory, 0, strrpos($directory, '\\'))), '\\');
		if ($directory && substr($directory, 0, 1) !== DIRECTORY_SEPARATOR)
			$directory = DIRECTORY_SEPARATOR . $directory;

		return $directory;
	}

	public static function prepareLoader($route, $class, $version=false, & $path=false, & $file=false,
										 $scaffolded=false, $created=false, $router=false)
	{
		$route = (substr($route, 0, 1) !== DIRECTORY_SEPARATOR) ? DIRECTORY_SEPARATOR . $route : $route;

		if ($router && $router->scaffold_root
			&& $router->scaffold_root != \Routerunner\Routerunner::$slim->config('templates.path')) {
			\Routerunner\Routerunner::$slim->config('templates.path',
				str_replace(\runner::config("SITEROOT"), '', $router->scaffold_root));
		}
		$path = realpath($router->scaffold_root . $route) . DIRECTORY_SEPARATOR;
		$model_class = substr($route, strrpos($route, DIRECTORY_SEPARATOR)+1);

		if ($file) {
			if (substr($file, 0, strlen($model_class)+1) != $model_class . ".") {
				$file = $model_class . "." . $file;
			}
		} else {
			if (strpos($class, DIRECTORY_SEPARATOR) !== false) {
				$file = substr($class, strrpos($class, DIRECTORY_SEPARATOR) + 1) .
					((substr($class, -1) == '.') ? '' : '.') . 'php';
			} elseif (substr($class, 0, strlen($model_class)) === $model_class) {
				$file = $class . ((substr($class, -1) == '.') ? '' : '.') . 'php';
			} else {
				$file = $model_class . '.' . $class . ((substr($class, -1) == '.') ? '' : '.') . 'php';
			}
		}

		$versionroute = '';
		if ($version) {
			if (!is_array($version)) {
				$version = array($version);
			}
			while (($version_row = array_shift($version)) && !$versionroute) {
				$directory = $path . $version_row;
				if (file_exists($directory)) {
					$versionroute = $version_row . DIRECTORY_SEPARATOR;
				}
			}
		}

		if (\runner::config("mode") == "backend" && file_exists($path.$versionroute.'backend'.DIRECTORY_SEPARATOR)) {
			$backendroute = 'backend'.DIRECTORY_SEPARATOR;
			if (!$created && \runner::stack("model_create")) {
				$created = \runner::stack("model_create");
			}
			if ($created && file_exists($path.$versionroute.$backendroute."create".DIRECTORY_SEPARATOR.$file)) {
				$versionroute = $versionroute.$backendroute."create".DIRECTORY_SEPARATOR;
			} elseif (file_exists($path.$versionroute.$backendroute.$file)) {
				$versionroute = $versionroute.$backendroute;
			}
		}
		$model_root = $router->scaffold_root . DIRECTORY_SEPARATOR . "model" . DIRECTORY_SEPARATOR .
			$model_class . DIRECTORY_SEPARATOR;

		if (file_exists($path.$versionroute.$file)) {
			if ($scaffolded) {
				return str_replace($router->scaffold_root, '', $path) . $versionroute . $file;
			} else {
				return $path . $versionroute . $file;
			}
		} elseif (file_exists($model_root . $file)) {
			if ($scaffolded) {
				return str_replace($router->scaffold_root, '', $model_root) . $file;
			} else {
				return $model_root . $file;
			}
		} else {
			return false;
		}
	}

    public static function loader(\Routerunner\BaseRunner $runner, $class, & $output=array())
    {
		$route = $runner->path . $runner->route;
		$version = $runner->version;
		$class = trim($class, '. \\/');
		$model_created = false;
		if ($runner->model && is_object($runner->model) &&
			((isset($runner->model->created) && $runner->model->created)) ||
			(isset($runner->override["create"]) && $runner->override["create"] < 0)) {
			$model_created = true;
		}
		$p = false;
		$f = false;
		$path = self::prepareLoader($route, $class, $version, $p, $f,
			false, $model_created, $runner->router);
		if (file_exists($path)) {
            //require $path;
			$vars_before = get_defined_vars();

			$sections = explode('.', $class);

			\Routerunner\Routerunner::$context = $route;

			//todo: check for redeclared classes & skip errors?
			if ((strpos($path, '.backend.') === false && strpos($path, '.model.') !== false
					&& strpos($path, '.model.params.') === false)
				|| strpos($path, '.runner.') !== false || strpos($path, '.function.') !== false) {
				$returned = (include_once $path);
			} elseif (strpos($path, '.return.') !== false) {
				ob_start();
				if ($returned = (@include $path)) {
					$returned = ob_get_contents();
				} else {
					$returned = false;
				}
				ob_end_clean();
			} else {
				$returned = (@include $path);
				if (!$returned) {
					$returned = true;
				}
			}

			if ($returned) {
				$output = array_diff_key(get_defined_vars(), $vars_before);
				unset($output['vars_before'], $output['returned']);

				if (!isset($output) || !is_array($output))
					$output = array();

				if (strpos($class, "external") !== false && (count($output) == 1 && isset($output["sections"]))) {
					$output["debug"] = true;
				}

				foreach ($output as $key => $val) {
					if (strpos($class, ".backend") === false && !is_string($val) && is_callable($val)) {
						$runner->functions[$key] = $val;
					} elseif (strpos($class, "i18n") !== false && $key == "i18n") {
						$runner->i18n = $val;
						$addon_path = \runner::config("SITEROOT") . \runner::config("scaffold") . DIRECTORY_SEPARATOR .
							'i18n' . $runner->route . DIRECTORY_SEPARATOR . $f;
						if (file_exists($addon_path)) {
							if (($i18n_addon = (@include $addon_path)) && is_array($i18n_addon)) {
								if (!is_array($runner->i18n)) {
									$runner->i18n = array();
								}
								$runner->i18n = array_merge($runner->i18n, $i18n_addon);
							}
						}
					} elseif (strpos($class, "script") !== false) {
						$runner->script = $returned;
					} elseif (strpos($class, "external") !== false) {
						if (!isset($runner->external)) {
							$runner->external = array(
								"models" => $returned,
								"params" => array(),
							);
						}
						$runner->external["params"][$key] = $val;
					} elseif (strpos($class, ".backend") !== false
						&& \runner::config('mode') == "backend" && isset($sections[2])) {
						$key = $sections[2];
						if (is_callable($returned)) {
							$runner->backend_context[$key] = $returned;
						} elseif (is_array($returned)) {
							$runner->backend_context[$key] = $returned;
							if (isset($runner->backend_context["global"])) {
								$runner->backend_context[$key] = array_merge($runner->backend_context["global"],
									$runner->backend_context[$key]);
							}
						} else {
							$runner->backend_context[$key] = array();
						}
					} elseif (substr($class, -13) == ".model.params") {
						$runner->model_context[$key] = $val;
					} elseif (substr($class, -6) == ".input") {
						$runner->form_context[$sections[1]][$key] = $val;
					} elseif (substr($class, -10) == ".construct") {
						$flash_var = $route . DIRECTORY_SEPARATOR . 'construct' . DIRECTORY_SEPARATOR . $key;
						\Routerunner\Routerunner::$slim->flashNow($flash_var, $val);
					} elseif (substr($class, -9) == ".destruct") {
						$flash_var = $route . DIRECTORY_SEPARATOR . 'destruct' . DIRECTORY_SEPARATOR . $key;
						\Routerunner\Routerunner::$slim->flashNow($flash_var, $val);
					} elseif (is_scalar($val) || is_array($val)) {
						$runner->context[$key] = $val;
					} elseif (is_resource($val)) {
						$runner->resources[$key] = $val;
					}
				}
				unset($runner->context["sections"]);

				if (substr($class, -6) == ".model" && strpos($class, ".backend") === false) {
					$pager = array();

					$override = (isset($runner->override) ? $runner->override : array());

					$namespace = trim(str_replace(DIRECTORY_SEPARATOR, '\\', $runner->path), '\\');
					$model_class = (isset($runner->model_context["model"]))
						? $runner->model_context["model"] : trim($runner->route, ' '.DIRECTORY_SEPARATOR);
					$table_from = (isset($runner->model_context["from"])
						? $runner->model_context["from"] : $model_class);
					$table_id = (isset($runner->override["id"])
						? $runner->override["id"] : null);
					$blankStdModel = false;
					$path = false;
					$file = false;
					if ($blank_path = self::prepareLoader('/model' . DIRECTORY_SEPARATOR . $model_class,
						$model_class . '.model', $version, $path, $file, false, false, $runner->router)) {
						$returned = (include_once $blank_path);
						$blank_ns = 'model\\' . $model_class;
						$blankStdModel = new $blank_ns($route, array(), $table_from, $table_id, $override, $runner->model_context);
						//$blankStdModel = new $blank_ns();
					}

					if (class_exists($namespace . '\\' . $model_class)) {
						$namespace_class = $namespace . '\\' . $model_class;
						$stdModel = new $namespace_class($route, array(), $table_from, $table_id, $override, $runner->model_context);
						if ($blankStdModel) {
							$stdModel->set($blankStdModel);
						}
						if (isset($runner->external) && $runner->external) {
							$runner->model = $namespace_class::set_models($runner->external["models"], $stdModel,
								(isset($runner->external["params"]["pkid"]) ? $runner->external["params"]["pkid"] : "id"),
								(isset($runner->external["params"]["from"]) ? $runner->external["params"]["from"] : false),
								(isset($runner->external["params"]["random"]) ? $runner->external["params"]["random"] : false),
								(isset($runner->external["params"]["session"]) ? $runner->external["params"]["session"] : false)
							);
						} else {
							$runner->model = $namespace_class::load($runner->model_context, $stdModel, $pager);
						}
						if (isset($pager))
							$runner->pager = $pager;
					} elseif (class_exists('model\\' . $model_class)) {
						$namespace_class = 'model\\' . $model_class;
						$stdModel = new $namespace_class($route, array(), $table_from, $table_id, $override, $runner->model_context);
						if ($blankStdModel) {
							$stdModel->set($blankStdModel);
						}
						if (isset($runner->external) && $runner->external) {
							$runner->model = $namespace_class::set_models($runner->external["models"], $stdModel,
								(isset($runner->external["params"]["pkid"]) ? $runner->external["params"]["pkid"] : "id"),
								(isset($runner->external["params"]["from"]) ? $runner->external["params"]["from"] : false),
								(isset($runner->external["params"]["random"]) ? $runner->external["params"]["random"] : false),
								(isset($runner->external["params"]["session"]) ? $runner->external["params"]["session"] : false)
								);
						} else {
							$runner->model = $namespace_class::load($runner->model_context, $stdModel, $pager);
						}
						if (isset($pager))
							$runner->pager = $pager;
					} elseif (class_exists($model_class)) {
						$stdModel = new $model_class($route, array(), $table_from, $table_id, $override, $runner->model_context);
						//$stdModel = new $model_class();
						if (isset($runner->external) && $runner->external) {
							$runner->model = $model_class::set_models($runner->external["models"], $stdModel,
								(isset($runner->external["params"]["pkid"]) ? $runner->external["params"]["pkid"] : "id"),
								(isset($runner->external["params"]["from"]) ? $runner->external["params"]["from"] : false),
								(isset($runner->external["params"]["random"]) ? $runner->external["params"]["random"] : false),
								(isset($runner->external["params"]["session"]) ? $runner->external["params"]["session"] : false)
							);
						} else {
							$runner->model = $model_class::load($runner->model_context, $stdModel, $pager);
						}
						if (isset($pager))
							$runner->pager = $pager;
					}
				}
			}
			return $returned;
		} elseif ($version) {
			$p = false;
			$f = false;
			$path = self::prepareLoader($route, $class, false, $p, $f, false, false, $runner->router);
			if (!isset($output) || !is_array($output))
				$output = array();
			if (file_exists($path)) {
				require $path;
				return true;
			}
		}


		if (!isset($output) || !is_array($output))
			$output = array();
		return false;
    }

	public static function includeRoute(\Routerunner\Router $router, $class='runner', $version=false) {
		$runner = new \Routerunner\BaseRunner($router);
		$runner->version = $version;

		if (self::loader($runner, $class)) {
			if (strpos($router->route, \Routerunner\Helper::$scaffold_class) !== false) {

			}
			$ns = '\\'.str_replace(DIRECTORY_SEPARATOR, '\\', trim($router->route, DIRECTORY_SEPARATOR)).'\\runner';
			$ns = str_replace('~', '', $ns); // home scaffold directory
			$router->$class = new $ns($router);
			$router->$class->version = $version;
			$router->route = $router->runner->route;
			Routerunner::setInstance($router);
			return $router;
		} else {
			// exception: class not found
			return false;
		}
	}

	public static function loadParser($pattern, \Routerunner\BaseRunner $runner=null, & $pattern_value=false)
	{
		if (strpos($pattern, ':view|list:') !== false) {
			$pattern_value = (!isset($runner->model) || (count($runner->model) <= 1)) ? 'view' : 'list';
			if (isset($runner->model_context['force_list']) && $runner->model_context['force_list'] === true) {
				$pattern_value = 'list';
			} elseif (isset($runner->model_context['force_view']) && $runner->model_context['force_view'] === true) {
				$pattern_value = 'view';
			}
			return str_replace(':view|list:', $pattern_value.'\.', $pattern);
		} elseif (strpos($pattern, ':lang:') !== false) {
			$pattern_value = false;
			$pattern_value = Routerunner::get('lang');
			if ($runner && isset($runner->path) && (strpos($runner->path, "/backend/") === 0)
				&& ($backend_lang_id = \runner::config('backend_language'))) {
				if (is_numeric($backend_lang_id)
					&& ($pattern_result = \db::query('SELECT code FROM `{PREFIX}lang` WHERE id = ?', array($backend_lang_id)))) {
					$pattern_value = $pattern_result[0]['code'];
				} else {
					$pattern_value = $backend_lang_id;
				}
			} elseif ((!isset($pattern_value) || !$pattern_value) && ($lang_id = \runner::config('language'))) {
				if ($pattern_result = \db::query('SELECT code FROM `{PREFIX}lang` WHERE id = ?', array($lang_id))) {
					$pattern_value = $pattern_result[0]['code'];
				}
			}
			if ($pattern_value) {
				$pattern_value .= '.';
			}
			return str_replace(':lang:', $pattern_value, $pattern);
		} elseif (strpos($pattern, ':owner:') !== false) {
			$me = \user::me();
			$uid = (int) $me;
			$pattern_value = '';

			// todo: rework to list models to use listitem's owner & group
			$model = ((is_array($runner->model) && isset($runner->model[0])) ? $runner->model[0] : $runner->model);

			$owner = (isset($model, $model->owner))
				? $model->owner
				: ((isset($runner->owner)) ? $runner->owner : false);

			if ($owner !== false && $owner === $uid)
				$pattern_value = 'owner';
			return (!$pattern_value) ? '' : str_replace(':owner:', $pattern_value.'\.', $pattern);
		} elseif (strpos($pattern, ':group:') !== false) {
			$email = null;
			$name = null;
			$group = null;
			\user::me($email, $name, $group);
			$gid = (int) $group;
			$pattern_value = '';

			// todo: rework to list models to use listitem's owner & group
			$model = ((is_array($runner->model) && isset($runner->model[0])) ? $runner->model[0] : $runner->model);

			$group = (isset($model, $model->group))
				? $model->group
				: ((isset($runner->group)) ? $runner->group : false);
			if ($group !== false && $group === $gid)
				$pattern_value = 'group';
			return (!$pattern_value) ? '' : str_replace(':group:', $pattern_value.'\.', $pattern);
		} elseif (strpos($pattern, ':owner|group:') !== false) {
			$email = null;
			$name = null;
			$group = null;
			$me = \user::me($email, $name, $group);
			$uid = (int) $me;
			$gid = (int) $group;
			$pattern_value = '';

			// todo: rework to list models to use listitem's owner & group
			$model = ((is_array($runner->model) && isset($runner->model[0])) ? $runner->model[0] : $runner->model);

			$owner = (isset($model, $model->owner))
				? $model->owner
				: ((isset($runner->owner)) ? $runner->owner : false);
			$group = (isset($model, $model->group))
				? $model->group
				: ((isset($runner->group)) ? $runner->group : false);
			if ($owner !== false && $owner === $uid && $group !== false && $group === $gid)
				$pattern_value = 'owner|group';
			elseif ($owner !== false && $owner === $uid)
				$pattern_value = 'owner';
			elseif ($group !== false && $group === $gid)
				$pattern_value = 'group';
			return (!$pattern_value) ? '' : str_replace(':owner|group:', $pattern_value.'\.', $pattern);
		} elseif (strpos($pattern, ':group|owner:') !== false) {
			$email = null;
			$name = null;
			$group = null;
			$me = \user::me($email, $name, $group);
			$uid = (int) $me;
			$gid = (int) $group;
			$pattern_value = '';

			// todo: rework to list models to use listitem's owner & group
			$model = ((is_array($runner->model) && isset($runner->model[0])) ? $runner->model[0] : $runner->model);

			$owner = (isset($model, $model->owner))
				? $model->owner
				: ((isset($runner->owner)) ? $runner->owner : false);
			$group = (isset($model, $model->group))
				? $model->group
				: ((isset($runner->group)) ? $runner->group : false);
			if ($group !== false && $group === $gid)
				$pattern_value = 'group';
			elseif ($owner !== false && $owner === $uid)
				$pattern_value = 'owner';
			return (!$pattern_value) ? '' : str_replace(':group|owner:', $pattern_value.'\.', $pattern);
		} elseif (strpos($pattern, ':request:') !== false) {
			$pattern_value = Routerunner::get('request');
			return str_replace(':request:', $pattern_value.'\.', $pattern);
		} else {
			$pattern_value = $pattern;
			return $pattern.'\.';
		}
	}

	public static function parse_variable($elem, $var)
	{
		if (!is_array($var)) {
			if (preg_match_all('/(\w|\d|_)+/i', $var, $matches)) {
				$path = $matches[0];
			} else {
				$path = array();
			}
		} else {
			//$path = array($var);
			$path = $var;
		}
		while ($variable = array_shift($path)) {
			if (is_object($elem) && isset($elem->$variable)) {
				$elem = $elem->$variable;
			} elseif (is_array($elem)) {
				if (isset($elem[$variable])) {
					$elem = $elem[$variable];
				} else {
					$model_array = $elem;
					$found = false;
					while (!$found && ($model = array_shift($model_array))) {
						if (is_object($model) && isset($model->$variable)) {
							$elem = $model->$variable;
							$found = true;
						}
					}
				}
			} else {
				$elem = '';
			}
		}
		return $elem;
	}


	public static function array_value_parse(& $item, $key, $elem) {
		if (strpos($key, '%') !== false && is_array($item) && isset($item[0])
			&& is_array($item[0]) && $elem) {

			$sprint_arr = array();
			foreach ($item as $sprint_value) {
				$sprint_arr[] = self::parse_variable($elem, $sprint_value);
			}
			$item = vsprintf($key, $sprint_arr);
		} elseif (strpos($key, '%') !== false && is_array($item) && $elem) {
			$item = sprintf($key, self::parse_variable($elem, $item));
		} elseif (strpos($key, '%') !== false && !is_array($item)) {
			$item = sprintf($key, $item);
		}
	}

	public static function parse_array($array, $elem=null, $except=array())
	{
		if (is_array($array)) {
			foreach ($array as $key => & $value) {
				if (!in_array($key, $except) && is_array($value)) {
					array_walk($value, array('self', 'array_value_parse'), $elem);
$array_keys = array_keys($value);
					if (is_array($value) && count($value) === 1 && strpos($array_keys[0], '%') !== false) {
						$value = array_shift($value);
					}

				}
			}
		}
		return $array;
	}

	public static function tree_route($tree, $route, $model=false) {
		if (!is_array($route)) {
			$route = array($route);
		}
		while (!is_null($branch = array_shift($route))) {
			$found = false;
			if ((is_subclass_of($model, '\Routerunner\BaseModel')) && $model->class == $branch) {
				if (isset($tree['children']['ref/' . $model->reference])) {
					$found = $tree['children']['ref/' . $model->reference];
				} elseif (isset($tree['children'][$model->class . '/' . $model->table_id])) {
					$found = $tree['children'][$model->class . '/' . $model->table_id];
				}
			}
			if ($found) {
				$tree = $found;
			} elseif (isset($tree['children'][$branch])) {
				$tree = $tree['children'][$branch];
			} elseif (!$found) {
				return array();
			}
		}
		return $tree;
	}

	public static function get_divisors($num) {
		$divisors = array();
		for($i = 1; $i <= $num; $i ++) {
			if ($num % $i == 0) {
				$divisors [] = $i;
			}
		}
		return $divisors;
	}
}

if (function_exists('mb_substr_replace') === false)
{
	function mb_substr_replace($string, $replacement, $start, $length = null, $encoding = null)
	{
		if (extension_loaded('mbstring') === true) {
			$string_length = (is_null($encoding) === true) ? mb_strlen($string) : mb_strlen($string, $encoding);
			if ($start < 0) {
				$start = max(0, $string_length + $start);
			} else if ($start > $string_length) {
				$start = $string_length;
			}

			if ($length < 0) {
				$length = max(0, $string_length - $start + $length);
			} else if ((is_null($length) === true) || ($length > $string_length)) {
				$length = $string_length;
			}

			if (($start + $length) > $string_length) {
				$length = $string_length - $start;
			}

			if (is_null($encoding) === true) {
				return mb_substr($string, 0, $start) . $replacement .
				mb_substr($string, $start + $length, $string_length - $start - $length);
			}

			return mb_substr($string, 0, $start, $encoding) . $replacement .
				mb_substr($string, $start + $length, $string_length - $start - $length, $encoding);
		}

		return (is_null($length) === true) ? substr_replace($string, $replacement, $start) : substr_replace($string, $replacement, $start, $length);
	}
}

function array_key_search($needle, $haystack) {
	if (isset($haystack[$needle])) {
		return $haystack[$needle];
	} else {
		foreach ($haystack as $key => $value) {
			if (is_array($value) && ($return = array_key_search($needle, $haystack))) {
				return $return;
			}
		}
	}
	return null;
}
