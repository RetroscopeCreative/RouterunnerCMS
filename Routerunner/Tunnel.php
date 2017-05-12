<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.12.05.
 * Time: 19:44
 */
const PERM_CREATE = 1;
const PERM_READ = 2;
const PERM_UPDATE = 4;
const PERM_DELETE = 8;
const PERM_ACTIVE = 16;
const PERM_MOVE = 32;

class tunnel
{
	static $object = null;
	static $stack = array();
	static $container = array();
	static $variables = array();

	public static function set($data=array())
	{
		static::reset();
		foreach ($data as $var => $value) {
			if (in_array($var, static::$variables)) {
				static::$$var = $value;
			} else {
				static::container($var, $value);
			}
		}
	}

	public static function get($name)
	{
		if (isset(static::$object) && is_object(static::$object) && isset(static::$object->$name)) {
			return static::$object->$name;
		} elseif (isset(static::$$name)) {
			return static::$$name;
		} else {
			return static::container($name);
		}
	}

	public static function container($name, $value=null)
	{
		if (!is_null($value)) {
			static::$container[$name] = $value;
		}
		return (isset(static::$container[$name]) ? static::$container[$name] : null);
	}

	public static function stack()
	{
		if (isset(static::$object) && static::$object) {
			static::$stack[] = static::$object;
		}
	}
	public static function unstack()
	{
		if (is_array(static::$stack) && static::$stack) {
			$object = array_pop(static::$stack);
			static::reset();
			static::$object = $object;
		}
	}
	public static function object($object)
	{
		static::reset();
		static::$object = $object;
	}

	public static function reset()
	{
		static::$object = null;
		$clname = (function_exists('get_called_class')) ? get_called_class() : static::$class;
		$class = new ReflectionClass($clname);
		$arr = array_keys($class->getStaticProperties());
		unset($arr[array_search('class', $arr)], $arr[array_search('stack', $arr)], $arr[array_search('object', $arr)],
			$arr[array_search('container', $arr)], $arr[array_search('variables', $arr)]);
		static::$variables = $arr;
		unset($arr[array_search('stack', $arr)]);
		foreach ($arr as $var) {
			static::$$var = null;
		}
		static::$container = array();
	}
}

class input extends tunnel
{
	static $class = 'input';
}

function input($name)
{
	$return = \input::get($name);
	if (is_null($return)) {
		$return = '';
	} elseif (is_array($return)) {
		$return = json_encode($return);
	}
	return $return;
}

class form extends tunnel
{
	static $name;
	static $method;
	static $xmethod;

	public static function input($field, $overwrite=array(), $formname=null)
	{
		$instance = rr::instance();
		return $instance->field($field, $overwrite, $formname);
	}

	public static function field_value($field, $formname=null)
	{
		$instance = rr::instance();
		return $instance->field_value($field, $formname);
	}
}

class model extends tunnel
{
	public static function load($context=array(), $route=false, & $router=false, $blank=false, $root=false)
	{
		$model = false;
		if (!$route
			&& (isset($context['self']['reference']) || isset($context["direct"]) || isset($context["resource"]))) {
			if (isset($context["direct"]) && is_numeric($context["direct"])
				|| (isset($context['self']['reference']) && is_numeric($context['self']['reference']))) {
				$SQL = 'SELECT model_class FROM {PREFIX}models WHERE reference = :reference';
				if ($result = \db::query($SQL, array(':reference' => (isset($context['self']['reference'])
					? $context['self']['reference'] : $context["direct"])))
				) {
					$route = '/model/' . $result[0]['model_class'];
				}
			} elseif (isset($context["direct"]) && is_array($context["direct"])) {
				$route = '/model/' .
					(is_numeric(key($context["direct"])) ? current($context["direct"]) : key($context["direct"]));
			} elseif (isset($context["resource"]) && is_array($context["resource"])
				&& isset($context["resource"][0], $context["resource"][1])) {
				$SQL = 'SELECT reference FROM {PREFIX}models WHERE model_class = :class AND table_id = :id';
				if ($result = \db::query($SQL,
					array(':class' => $context["resource"][0], ":id" => $context["resource"][1]))
				) {
					$route = '/model/' . $context["resource"][0];
					$context = array("direct" => $result[0]["reference"]);
				}
			} elseif (isset($context["resource"]) && !is_array($context["resource"])
				&& strpos($context["resource"], '/') !== false) {
				$context["resource"] = explode('/', $context["resource"]);
				$SQL = 'SELECT reference FROM {PREFIX}models WHERE model_class = :class AND table_id = :id';
				if ($result = \db::query($SQL,
					array(':class' => $context["resource"][0], ":id" => $context["resource"][1]))
				) {
					$route = '/model/' . $context["resource"][0];
					$context = array("direct" => $result[0]["reference"]);
				}
			}
		}
		if ($route && $context) {
			\runner::route($route, $context, $router, true, $blank, $root);
			if (isset($router->runner->model)) {
				$model = $router->runner->model;
			}
		}
		if ($model && is_array($model) && count($model) == 1) {
			$model = array_shift($model);
		}
		return $model;
	}
	public static function state($name, $model=null)
	{
		$tmp_object = false;
		if (!is_null($model)) {
			if (is_array($model) && count($model) == 1) {
				$model = array_shift($model);
			}
			if (is_object($model) && get_parent_class($model) == "Routerunner\\BaseModel") {
				$tmp_object = model::$object;
				model::$object = $model;
			} else {
				throw new \Exception('Invalid model passed to \\model::state function!');
			}
		}
		$return = (isset(model::$object->states[$name])) ? model::$object->states[$name] : null;
		if ($tmp_object) {
			model::$object = $tmp_object;
		}
		return $return;
	}
	public static function property($name, $model=null)
	{
		$tmp_object = false;
		if (!is_null($model)) {
			if (is_array($model) && count($model) == 1) {
				$model = array_shift($model);
			}
			if (is_object($model) && get_parent_class($model) == "Routerunner\\BaseModel") {
				$tmp_object = model::$object;
				model::$object = $model;
			} else {
				throw new \Exception('Invalid model passed to \\model::property function!');
			}
		}
		$return = (isset(model::$object->$name)) ? model::$object->$name : null;
		if ($tmp_object) {
			model::$object = $tmp_object;
		}
		return $return;
	}
	public static function url($force_class=false, $model=null)
	{
		$tmp_object = false;
		if (!is_null($model)) {
			if (is_array($model) && count($model) == 1) {
				$model = array_shift($model);
			}
			if (is_numeric($model)) {
				$model = \model::load(array("direct" => $model));
			} elseif (is_string($model) && strpos($model, "/") !== false) {
				$model_resource = explode("/", $model);
				$model = \model::load(array("resource" => $model_resource));
			}
			if ($model && is_object($model) && get_parent_class($model) == "Routerunner\\BaseModel") {
				$tmp_object = model::$object;
				model::$object = $model;
			} elseif ($model) {
				throw new \Exception('Invalid model passed to \\model::url function!');
			}
		}
		$return = "javascript:;";
		if (isset(model::$object) && is_object(model::$object)) {
			$return = model::$object->url($force_class);
		}
		if ($tmp_object) {
			model::$object = $tmp_object;
		}
		return $return;
	}
	public static function call()
	{
		$return = false;

		$args = func_get_args();

		$runner = \Routerunner\Routerunner::getParentInstance(false)->runner;
		if (!is_null($runner->model) && $runner->model == model::$object) {
			$fn = ((isset($args[0])) ? array_shift($args) : false);
			if ($fn
				&& !(isset($runner->functions[$fn])
					&& !is_string($runner->functions[$fn]) && is_callable($runner->functions[$fn]))) {
				$fn = false;
			}
			$arg = ((isset($args[0])) ? array_shift($args) : false);
			if ($arg) {
				if (is_string($arg) && !is_null(model::property($arg)) && count($args) == 0) {
					$return = $runner->functions[$fn](model::$object, model::property($arg), $arg, $args);
				} else {
					$return = $runner->functions[$fn](model::$object, null, $arg, $args);
				}
			} elseif (!empty($fn) && isset($runner->functions[$fn])) {
				$return = $runner->functions[$fn](model::$object, $args);
			}
		}
		return $return;
	}

	public static function parent($model) {
		$return = null;
		if (isset($model->parent) && is_object($model->parent)) {
			$return = $model->parent;
		} elseif (isset($model->parent) && is_numeric($model->parent) && $model->parent) {
			$context = array("direct" => $model->parent);
			$return = self::load($context);
		}
		return $return;
	}

	public static function insert($class, $table_id, $parent, $prev=0, $table_from=false, $lang=null) {
        if (\user::me()) {

            // check for rights

            if (!$table_from) {
                $table_from = $class;
            }

            $SQL = 'INSERT INTO `{PREFIX}models` (model_class, table_from, table_id) VALUES (:model_class, :table_from, :table_id)';
            if ($reference = \db::insert($SQL, array(
                ':model_class' => $class,
                ':table_from' => $table_from,
                ':table_id' => $table_id,
            ))) {

                $SQL = 'CALL `{PREFIX}tree_insert`(:reference, :parent, :prev, :lang)';
                if ($result = \db::query($SQL, array(
                    ':reference' => $reference,
                    ':parent' => $parent,
                    ':prev' => $prev,
                    ':lang' => $lang,
                ))) {
                    return $result[0]['inserted'];
                }

            }
        }
        return false;
    }

    public static function delete($reference) {
        if (\user::me()) {

            // check for rights

            $SQL = 'CALL `{PREFIX}tree_remove`(:reference)';
            if ($result = \db::query($SQL, array(':reference' => $reference))) {
                $SQL = 'DELETE FROM `{PREFIX}models` WHERE reference = :reference';
                \db::query($SQL, array(':reference' => $reference));

                return $result[0]['removed'];
            }

        }
        return false;
    }
}

class db extends tunnel
{
	public static function query($SQL, $params=array(), $flags=0, $force_query=false)
	{
		return \Routerunner\Db::query($SQL, $params, $flags, $force_query);
	}
	public static function insert($SQL, $params=array())
	{
		return \Routerunner\Db::insert($SQL, $params);
	}
	public static function escape($str) {
		return \Routerunner\Db::escape($str);
	}
}

class context extends tunnel
{
	public static function get($name=false)
	{
		$runner = rr::instance();
        if (!$name) {
            return $runner->context;
        } else {
            $return = (isset($runner->context[$name])) ? $runner->context[$name] : '';
            return $return;
        }
	}
}

class mail extends tunnel
{
	public static function mailer($route, $context=array(), $attachment=null)
	{
		return \Routerunner\Mail::mailer($route, $context, $attachment);
	}
}

class user extends tunnel
{
	public static function me(& $email=null, & $name=null, & $group=null, & $custom=array(), & $scope=null, & $auth=null, & $alias=false)
	{
		return \Routerunner\User::me($email, $name, $group, $custom, $scope, $auth, $alias);
	}
	public static function get($name)
	{
		return \Routerunner\User::get($name);
	}
	public static function token()
	{
		return \Routerunner\User::get("token");
	}
	public static function auth($main, & $sub = array())
	{
		return \Routerunner\User::auth($main, $sub);
	}
	public static function logout()
	{
		\Routerunner\User::logout();
	}
}

class session extends tunnel
{
    public static function open() {
        if (!($session_id = \runner::stack('session_id'))) {
            $token = \user::token();

            $SQL = 'CALL `{PREFIX}session_open`(0, :label, :token)';
            if ($session_result = \db::query($SQL, array(
                ':label' => NULL,
                ':token' => $token,
            ))
            ) {
                $session_id = $session_result[0]['session_opened'];

                \runner::stack("models_created", array(), true);

                \runner::stack('session_id', $session_id, true);
            }
        }
        return $session_id;
    }
}

class bootstrap extends tunnel
{
	public static function get($name=null)
	{
		if (!is_null($name)) {
			$return = \Routerunner\Bootstrap::bootstrap($name);
			if ($return === \Routerunner\Bootstrap::bootstrap()) {
				$return = false;
			}
			return $return;
		} else {
			return \Routerunner\Bootstrap::bootstrap();
		}
	}
}

class rr extends tunnel
{
	public static function instance()
	{
		return \Routerunner\Routerunner::getParentInstance(false)->runner;
	}

	public static function bootstrap()
	{
		return \Routerunner\Bootstrap::bootstrap();
	}
}