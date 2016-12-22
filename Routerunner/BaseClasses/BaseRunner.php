<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.18.
 * Time: 21:14
 */
namespace Routerunner;

use plugin\tag;

class BaseRunner
{
	public $router = false;
	public $route = false;
	//public $slim = null;
	public $scaffold_root = false;
	public $path = __NAMESPACE__;
	public $version = false;
	public $versionroute = '';
	public $files = array();
	public $section = array();
	public $load = array(
		array('event','(:request:)?','construct'),
		'function',
		array('i18n','(:lang:)+'),
		'property',
		array('model','params'),
		array('model','params', 'extend'),
		'external',
		'model',
        array('model','after'),
		array('script', 'return'),
		array('event','(:request:)?','load'),
		array('event','(:request:)?','before'),
		array('event','(:request:)?','after'),
		array('backend','global','(:owner|group:)?'),
		array('backend','container','(:owner|group:)?'),
		array('backend','model','(:owner|group:)?'),
		array('backend','develop','(:owner|group:)?'),
		array('(:view|list:)','(:owner|group:)?'),
		array('event','(:request:)?','destruct'),
	);
	public $parsers = array();

	public $context = array();
	public $i18n = array();
	public $script = array();
	public $view = false;
	public $form_context = array();
	public $form = array();
	public $model_context = array();
	public $model = false;
	public $functions = array();
	public $resources = array();
	public $event_load = false;
	public $event_before = false;
	public $event_after = false;

	public $override = array();

	public $backend_id = array();
	public $backend_context = array();

	public $unique = false;
	public $permission = false;

	public $cache_exp = -1;

	public $html = '';
	public $html_render = '';
	public $html_before = '';
	public $html_after = '';

	public function __construct(\Routerunner\Router $router)
	{
		$this->router = $router;
		$route = $router->route;

		$this->path = substr($route, 0, strrpos($route, DIRECTORY_SEPARATOR));
		$this->route = substr($route, strrpos($route, DIRECTORY_SEPARATOR));
		$this->scaffold_root = ((isset($router->scaffold_root) && $router->scaffold_root)
			? $router->scaffold_root : \Routerunner\Helper::$scaffold_root);
		if ($versions = \runner::config('version')) {
			if (!is_array($versions)) {
				$versions = array($versions);
			}
			$root_route = $this->scaffold_root . $this->path . $this->route . DIRECTORY_SEPARATOR;
			while (($version = array_shift($versions)) && !$this->versionroute) {
				$directory = $root_route . $version;
				if (file_exists($directory)) {
					$this->versionroute = DIRECTORY_SEPARATOR . $version;
				}
			}
		}
	}

	public function __destruct()
	{
		//$this->slim->run();
	}

	public function readable()
	{
		return ((!is_array($this->permission) || current($this->permission) & PERM_READ) ? true : false);
	}

	public static function route($route)
	{
		echo \Routerunner\Routerunner::route($route);
	}

	public function route_parser()
	{
		foreach ($this->load as $load) {
			$this->section = array();
			$file = '';

			if (isset($this->model, $this->model->permission)
				&& is_array($this->model->permission)) {
				$this->permission = $this->model->permission;
			}

			if (is_array($load)) {
				\Routerunner\Helper::loadParser($load[0], $this, $parsed_section);
				if (isset($this->files[$parsed_section])) {
					if (!is_array($this->files[$parsed_section]))
						$files = array($this->files[$parsed_section]);
					else
						$files = $this->files[$parsed_section];
					$regexp = str_replace('~', '', trim($this->route, DIRECTORY_SEPARATOR).'\.');
					foreach ($load as $section) {

						$parsed = \Routerunner\Helper::loadParser($section, $this, $parsed_value);

						if ($parsed_value)
							$regexp .= $parsed;

						$this->section[] = $parsed_value;
						$files = preg_grep('/\b'.$regexp.'/', $files);
					}
					$files = preg_grep('/^'.$regexp.'$/', $files);
					if (count($files) == 1) {
						$file = array_shift($files);
					} elseif (count($files) > 1) {
						$file = array_shift($files);
						while ($file_row = array_shift($files)) {
							if (strlen($file_row) > strlen($file))
								$file = $file_row;
						}
					} else {
						// exception: no file match
					}
				}
			} elseif (isset($this->files[$load])) {
				$this->section[] = $load;
				$file = $this->route.'.'.$load;
			}

			if ($file) {
				if (!$this->readable()) {
					$debug = 1;
				}
				if ($this->readable() && ($this->section[0] == 'view' || $this->section[0] == 'list')) {
					if (\runner::config('mode') != 'backend' && $this->router->cache_route
						&& ($html = $this->router->get_cache($_model))) {
						if (\runner::config('silent')) {
							$html = str_replace(array("\t", PHP_EOL . PHP_EOL), "", $html);
						}
						$this->html = $html;
						if ($_model) {
							$this->model = $_model;
						}
						return true;
					}

					\model::stack();

					Routerunner::$context = $this->context;
					\Routerunner\Helper::prepareLoader($this->route, $file, $this->version, $path, $class
						, false, false, $this->router);
					$this->view = $class;

                    if ($this->event_load) {
                        \Routerunner\Helper::loader($this, $this->event_load, $output);
                        $this->event_load = false;
                    }

					if (isset($this->model) && $this->model === false) {
						// todo: check view permission
						$this->render();
					} elseif (isset($this->model) && count($this->model) == 1 && ($this->section[0] != 'list'
							|| (isset($this->model_context['force_view'])
								&& $this->model_context['force_view'] === true))) {
						if (is_array($this->model)) {
							$this->model = array_shift($this->model);
						}
						if (!$this->model->readable()) {
							$debug = 1;
						}

						if ($this->model->readable()) {
							$explode = explode('\\', get_class($this->model));
							if ($explode[0] != "backend") {
								\model::object($this->model);
							}
							$this->render();
						}
					} elseif (isset($this->model)) {
						$models = $this->model;
						if (!is_array($models))
							$models = array($models);
						$this->render_list($models);
						$this->model = $models;
					} elseif (!isset($this->model) && isset($this->files['list']) && is_array($this->files['list'])
						&& in_array(trim($this->route, '/\\') . '.list.null.', $this->files['list'])) {
						$this->render_null();
					} elseif (!isset($this->model) && (isset($this->model_context['force_view'])
							&& $this->model_context['force_view'] === true)
						&& isset($this->files['view']) && is_array($this->files['view'])
						&& in_array(trim($this->route, '/\\') . '.view.null.', $this->files['view'])) {
						$this->render_null();
					} elseif (!isset($this->model) && (isset($this->model_context['force_view'])
							&& $this->model_context['force_view'] === true)) {
						$this->render();
					}

					\model::unstack();
				} elseif ($this->section[0] == 'event'
					&& ($this->section[count($this->section)-1] == 'before'
						|| $this->section[count($this->section)-1] == 'after'
						|| $this->section[count($this->section)-1] == 'load')) {
					if ($this->section[count($this->section) - 1] == 'before') {
						$this->event_before = $file;
					} elseif ($this->section[count($this->section) - 1] == 'after') {
						$this->event_after = $file;
					} elseif ($this->section[count($this->section) - 1] == 'load') {
						$this->event_load = $file;
					}
				} elseif ($this->section[0] == 'backend' && \runner::config('mode') == 'backend') {
					\Routerunner\Helper::loader($this, $file, $output);
					/*
					if ($this->section[1] == "model" && isset($this->backend_context["model"]["fields"])) {
						foreach ($this->backend_context["model"]["fields"] as $field_name => & $field_data) {
							if (!isset($field_data["default"])) {
								if (\runner::config("default." . $field_name)) {
									$field_data["default"] = \runner::config("default." . $field_name);
								} elseif (\runner::config("default.type." . $field_data["type"])) {
									$field_data["default"] = \runner::config("default.type." . $field_data["type"]);
								}
							}
						}
						$_model = false;
						if (isset($this->model) && $this->model && is_array($this->model) && isset($this->model[0]->route)) {
							$_model = $this->model[0];
						} elseif (isset($this->model) && $this->model && isset($this->model->route)) {
							$_model = $this->model;
						}
						if (($model_create = \runner::stack("model_create")) && $_model &&
							isset($model_create["route"]) && $model_create["route"] == $_model->route) {
							foreach ($this->backend_context["model"]["fields"] as $field_name => $field_data) {
								if (isset($field_data["default"])) {
									$_model->$field_name = $field_data["default"];
								}
							}
						}
					}
					*/
				} elseif (\runner::config('mode') == 'backend' || $this->section[0] != 'backend') {
					\Routerunner\Helper::loader($this, $file, $output);
				}
			}
		}
	}

	public function render($list_index=null)
	{
	    if (\runner::now('skip_render')) {
	        return '';
        }
		$html = '';
		if ($this->event_load) {
			\Routerunner\Helper::loader($this, $this->event_load, $output);
			$this->event_load = false;
		}
		if ($this->event_before)
			\Routerunner\Helper::loader($this, $this->event_before, $output);

		$html_path = $this->router->get_route();
		if (!\runner::config('silent')) {
			$html .= '<!--Routerunner::Route('.$html_path.')//-->'.PHP_EOL;
		}
		//$view = $this->path . $this->route . $this->versionroute . DIRECTORY_SEPARATOR . $this->view;
		$file = $this->view;
		$path = "";
		$model_created = false;
		if ((isset($this->model->created) && $this->model->created) ||
			(isset($this->override["create"]) && $this->override["create"] < 0)) {
			$model_created = true;
		}
		$found = false;
		if (!is_null($list_index)) {
			if ($divisors = \Routerunner\Helper::get_divisors($list_index)) {
				foreach ($divisors as $divisor) {
					$divisor_file = str_replace('.php', '.div' . $divisor . '.php', $this->view);
					if (!$found && ($view = \Routerunner\Helper::prepareLoader($this->path . $this->route,
							false, $this->versionroute, $path, $divisor_file, true, $model_created, $this->router))) {
						$found = $view;
					}
				}
			}
		}
		if (!$found) {
			$view = \Routerunner\Helper::prepareLoader($this->path . $this->route,
				false, $this->versionroute, $path, $file, true, $model_created, $this->router);
		}
		if ($view) {
			$html .= \Routerunner\Routerunner::$slim->render($view, array('runner' => $this));
		}
		if ($this->i18n) {
			$html = str_replace(array_keys($this->i18n), array_values($this->i18n), $html);
		}
		$html = $this->backend($html);
		//$html = $this->backend_container($html);

		$this->html .= $html;
		\Routerunner\Routerunner::process($this);

		if ($this->event_after)
			\Routerunner\Helper::loader($this, $this->event_after, $output);

		if ($this->script) {
			$this->script = preg_replace(array('/<script[^>]+>/', '/<\/script>/'), '', $this->script);
			\runner::stack_js($this->script);
		}
		$this->html_render = $this->html;
	}

	public function render_list(&$models=array())
	{
        if (\runner::now('skip_render')) {
            return '';
        }
		if ($this->event_load) {
			\Routerunner\Helper::loader($this, $this->event_load, $output);
			$this->event_load = false;
		}
		//$view = $this->path . $this->route . $this->versionroute . DIRECTORY_SEPARATOR . str_replace('.php', '.before.php', $this->view);
		$file = str_replace('.php', '.before.php', $this->view);
		$path = "";
		$model_created = false;
		if ((isset($this->model->created) && $this->model->created) ||
			(isset($this->override["create"]) && $this->override["create"] < 0)) {
			$model_created = true;
		}
		$this->html_before = '';
		$view = \Routerunner\Helper::prepareLoader($this->path . $this->route,
			false, $this->versionroute, $path, $file, true, $model_created, $this->router);
		if ($view && file_exists($this->scaffold_root . $view)) {
			$html_path = $this->router->get_route();
			if (!\runner::config('silent')) {
				$this->html_before .= '<!--Routerunner::Route('.$html_path.'.before)//-->'.PHP_EOL;
			}
			$this->html_before .= \Routerunner\Routerunner::$slim->render($view, array('runner' => $this));
			if ($this->i18n) {
				$this->html_before = str_replace(array_keys($this->i18n), array_values($this->i18n), $this->html_before);
			}
			\Routerunner\Routerunner::process($this);
		}

		$i = 0;
		foreach ($models as $index => $model) {
			if (!$model->readable()) {
				$debug = 1;
			}
			if ($model->readable()) {
				$this->model = $model;
				$explode = explode('\\', get_class($this->model));
				if ($explode[0] != "backend") {
					\model::object($this->model);
				}
				if (!$this->render_eq($i)) {
					$this->render($i + 1);
				}
				$i++;
			}
		}
		//$view = $this->path . $this->route . $this->versionroute . DIRECTORY_SEPARATOR . str_replace('.php', '.after.php', $this->view);
		$file = str_replace('.php', '.after.php', $this->view);
		$path = "";
		$model_created = false;
		if ((isset($this->model->created) && $this->model->created) ||
			(isset($this->override["create"]) && $this->override["create"] < 0)) {
			$model_created = true;
		}
		$this->html_after = '';
		$view = \Routerunner\Helper::prepareLoader($this->path . $this->route,
			false, $this->versionroute, $path, $file, true, $model_created, $this->router);

		if ($view && file_exists($this->scaffold_root . $view)) {
			$html_path = $this->router->get_route();
			if (!\runner::config('silent')) {
				$this->html_after .= '<!--Routerunner::Route('.$html_path.'.after)//-->'.PHP_EOL;
			}
			$this->html_after .= \Routerunner\Routerunner::$slim->render($view, array('runner' => $this));
			if ($this->i18n) {
				$this->html_after = str_replace(array_keys($this->i18n), array_values($this->i18n), $this->html_after);
			}
			\Routerunner\Routerunner::process($this);
		}
		if ($this->script) {
			\runner::stack_js($this->script);
		}

		$this->html = $this->backend_container($this->html_before . $this->html . $this->html_after);
	}

	public function render_eq($index)
	{
		$returned = false;
		//$view = $this->path . $this->route . $this->versionroute . DIRECTORY_SEPARATOR . str_replace('.php', '.eq' . $index . '.php', $this->view);
		$file = str_replace('.php', '.eq' . $index . '.php', $this->view);
		$path = "";
		$model_created = false;
		if ((isset($this->model->created) && $this->model->created) ||
			(isset($this->override["create"]) && $this->override["create"] < 0)) {
			$model_created = true;
		}
		$view = \Routerunner\Helper::prepareLoader($this->path . $this->route,
			false, $this->versionroute, $path, $file, true, $model_created, $this->router);
		if ($view && file_exists($this->scaffold_root . $view)) {
			$html_path = $this->router->get_route();
			if (!\runner::config('silent')) {
				$this->html .= '<!--Routerunner::Route('.$html_path.'.eq' . $index . ')//-->'.PHP_EOL;
			}
			$this->html .= \Routerunner\Routerunner::$slim->render($view, array('runner' => $this));
			if ($this->i18n) {
				$this->html = str_replace(array_keys($this->i18n), array_values($this->i18n), $this->html);
			}
			$this->html = $this->backend($this->html);

			\Routerunner\Routerunner::process($this);
			$returned = true;
		}
		$this->html_render = $this->html;
		return $returned;
	}

	public function render_null()
	{
        if (\runner::now('skip_render')) {
            return '';
        }
		//$view = $this->path . $this->route . $this->versionroute . DIRECTORY_SEPARATOR . str_replace('.php', '.before.php', $this->view);
		$file = str_replace('.php', '.before.php', $this->view);
		$path = "";
		$model_created = false;
		if ((isset($this->model->created) && $this->model->created) ||
			(isset($this->override["create"]) && $this->override["create"] < 0)) {
			$model_created = true;
		}
		$this->html_before = '';
		$view = \Routerunner\Helper::prepareLoader($this->path . $this->route,
			false, $this->versionroute, $path, $file, true, $model_created, $this->router);
		if ($view && file_exists($this->scaffold_root . $view)) {
			$html_path = $this->router->get_route();
			if (!\runner::config('silent')) {
				$this->html_before .= '<!--Routerunner::Route('.$html_path.'.before)//-->'.PHP_EOL;
			}
			$this->html_before .= \Routerunner\Routerunner::$slim->render($view, array('runner' => $this));
			if ($this->i18n) {
				$this->html_before = str_replace(array_keys($this->i18n), array_values($this->i18n), $this->html_before);
			}
			\Routerunner\Routerunner::process($this);
		}

		//$view = $this->path . $this->route . $this->versionroute . DIRECTORY_SEPARATOR . str_replace('.php', '.null.php', $this->view);
		$file = str_replace('.php', '.null.php', $this->view);
		$path = "";
		$model_created = false;
		if ((isset($this->model->created) && $this->model->created) ||
			(isset($this->override["create"]) && $this->override["create"] < 0)) {
			$model_created = true;
		}
		$view = \Routerunner\Helper::prepareLoader($this->path . $this->route,
			false, $this->versionroute, $path, $file, true, $model_created, $this->router);
		if ($view && file_exists($this->scaffold_root . $view)) {
			$html_path = $this->router->get_route();
			if (!\runner::config('silent')) {
				$this->html .= '<!--Routerunner::Route('.$html_path.'.null)//-->'.PHP_EOL;
			}
			$this->html .= \Routerunner\Routerunner::$slim->render($view, array('runner' => $this));
			if ($this->i18n) {
				$this->html = str_replace(array_keys($this->i18n), array_values($this->i18n), $this->html);
			}
			\Routerunner\Routerunner::process($this);
			$this->html_render = $this->html;
		}

		//$view = $this->path . $this->route . $this->versionroute . DIRECTORY_SEPARATOR . str_replace('.php', '.after.php', $this->view);
		$file = str_replace('.php', '.after.php', $this->view);
		$path = "";
		$model_created = false;
		if ((isset($this->model->created) && $this->model->created) ||
			(isset($this->override["create"]) && $this->override["create"] < 0)) {
			$model_created = true;
		}
		$this->html_after = '';
		$view = \Routerunner\Helper::prepareLoader($this->path . $this->route,
			false, $this->versionroute, $path, $file, true, $model_created, $this->router);
		if ($view && file_exists($this->scaffold_root . $view)) {
			$html_path = $this->router->get_route();
			if (!\runner::config('silent')) {
				$this->html_after .= '<!--Routerunner::Route('.$html_path.'.after)//-->'.PHP_EOL;
			}
			$this->html_after .= \Routerunner\Routerunner::$slim->render($view, array('runner' => $this));
			if ($this->i18n) {
				$this->html_after = str_replace(array_keys($this->i18n), array_values($this->i18n), $this->html_after);
			}
			\Routerunner\Routerunner::process($this);
		}

		$this->html = $this->backend_container($this->html_before . $this->html . $this->html_after);
	}

	public function form($formname=null)
	{
		$formname = (is_null($formname) && count(array_keys($this->form)))
			? array_shift(array_keys($this->form)) : $formname;
		$this->currentform = $formname;
		if (!is_null($formname)) {
			$html_path = $this->router->get_route() . '\\' .
				substr($this->form[$formname]->view, 0, strrpos($this->form[$formname]->view, '.'));
			$formhtml = '';
			if (!\runner::config('silent')) {
				$formhtml .= '<!--Routerunner::Route(' . $html_path . ')//-->'.PHP_EOL;
			}
			$formhtml .= \Routerunner\Routerunner::$slim->render($this->path . $this->route . $this->versionroute
				. DIRECTORY_SEPARATOR . $this->form[$formname]->view, array('runner' => $this));

			$formhtml .= $this->plugins("form.min.js");
			if ($this->i18n) {
				$formhtml = str_replace(array_keys($this->i18n), array_values($this->i18n), $formhtml);
			}
			return \Routerunner\Routerunner::process($this, $formhtml);
		}
	}

	public function field($field, $overwrite=array(), $formname=null)
	{
		if (is_null($formname) && isset($this->currentform) && isset($this->form[$this->currentform])) {
			$formname = $this->currentform;
		}
		$formname = (is_null($formname) && count(array_keys($this->form)))
			? array_shift(array_keys($this->form)) : $formname;
		if (!is_null($formname) && isset($this->form[$formname], $this->form_context[$formname]['input'][$field])) {
			$value = null;
			if (isset($this->model, $this->model->$field)) {
				$value = $this->model->$field;
			}
			return $this->form[$formname]->field($field, $value, $overwrite);
		}
	}

	public function field_value($field, $formname=null)
	{
		if (is_null($formname) && isset($this->currentform) && isset($this->form[$this->currentform])) {
			$formname = $this->currentform;
		}
		$formname = (is_null($formname) && count(array_keys($this->form)))
			? array_shift(array_keys($this->form)) : $formname;
		if (!is_null($formname) && isset($this->form[$formname], $this->form_context[$formname]['input'][$field])) {
			$value = null;
			if (isset($this->model, $this->model->$field)) {
				$value = $this->model->$field;
			} elseif (isset($this->form_context[$formname]['input'][$field]['value'])) {
				$value = $this->form_context[$formname]['input'][$field]['value'];
			}
			return $value;
		}
		return null;
	}

	public function parent($property=null, $return='model', $runner=false)
	{
	    if (!$runner) {
	        $runner = $this;
        }
		$value = false;
		if (isset($runner->router, $runner->router->parent) && is_object($runner->router->parent)
			&& is_a($runner->router->parent, "Routerunner\\Router")) {
			$parent_router = $runner->router->parent;
			if ($return == 'model' && isset($parent_router->runner, $parent_router->runner->model)) {
				if (isset($property, $parent_router->runner->model->$property)) {
					$value = $parent_router->runner->model->$property;
				} elseif (is_null($property) && $parent_router->runner->model) {
					$value = $parent_router->runner->model;
				} elseif (is_null($property)) {
					$value = $parent_router->runner;
				}
			} elseif ($return == 'runner' && isset($parent_router->runner)) {
				if (isset($property, $parent_router->runner->$property)) {
					$value = $parent_router->runner->$property;
				} elseif (is_null($property)) {
					$value = $parent_router->runner;
				}
			} elseif ($return == 'router') {
				if (isset($property, $parent_router->$property)) {
					$value = $parent_router->$property;
				} elseif (is_null($property)) {
					$value = $parent_router;
				}
			}
		}
		return $value;
	}

    public function model_parent()
    {
        $runner = $this;
        while ($runner && ($parent = $this->parent(null, 'runner', $runner))) {
            $runner = $parent;
            if (isset($runner->model) && !empty($runner->model)) {
                return $runner->model;
            }
        }
        return false;
    }

	public function plugins($script, $callback='false') {
		$plugins_loaded = \runner::stack("plugins_loaded");
		if (!is_array($plugins_loaded)) {
			$plugins_loaded = array();
		}
		if (!isset($plugins_loaded[$script])) {
			$plugin_dir = \runner::config("BASE") . \runner::config("ROUTERUNNER_BASE");
			$plugins_loaded[$script] = true;
			\runner::stack("plugins_loaded", $plugins_loaded);
			\runner::stack_js(<<<HTML
if (typeof routerunner_base != "function") {
	var script_elem = document.createElement("script");
	script_elem.setAttribute("type", "text/javascript");
	script_elem.setAttribute("src", "{$plugin_dir}plugins/base.min.js");
	document.getElementsByTagName("head")[0].appendChild(script_elem);
	script_elem.addEventListener("load", function() {
		routerunner_base().load_script("{$plugin_dir}plugins/{$script}", {$callback}, true);
	});
} else {
	routerunner_base().load_script("{$plugin_dir}plugins/{$script}", {$callback}, true);
}

HTML
);
		}
	}

	private function backend($html){
		if (\runner::config('mode') == 'backend') {
			if ($this->model && $this->model->permission  && !($this->model->writable()
					|| $this->model->deletable() || $this->model->activate_allowed() || $this->model->movable())) {
				return $html;
			}
			if ((isset($this->model_context['skip_backend']) && $this->model_context['skip_backend'] === true)) {
				return $html;
			}
			if (isset($this->backend_context['model']) && isset($this->model) && $this->model) {
				$html = $this->backend_model_wrapper($html);
			}
			if (isset($this->backend_context['container'])) {
				$html = $this->backend_container($html);
			}
		}
		return $html;
	}
	private function backend_model_wrapper($html)
	{
		$html = $this->backend_wrapper($html, 'model');

		if ($this->backend_id) {
			$script = '';
			foreach ($this->backend_id as $backend_id) {
				$script .= 'routerunner_attach("' . $backend_id . '");' . PHP_EOL;
			}

			\runner::stack_js($script);
			/*
			$html .= <<<HTML
<script id="script_{$backend_id}">{$script}</script>

HTML;
			*/
		} else {
			// todo: throw exception
		}

		return $html;
	}
	private function backend_container($html){
		if (\runner::config('mode') == 'backend') {
			if (isset($this->backend_context['container'])) {
				$process = true;

				if (is_callable($this->backend_context['container'])) {
					$context = $this->backend_context['container']($this);
				} elseif (is_array($this->backend_context['container'])) {
					$context = $this->backend_context['container'];
				}
				if ($context && array_keys($context) === range(0, count($context) - 1)) {
					// multiple container context
					foreach ($context as $container_context) {
						$html = $this->backend_container_wrapper($html, $container_context);
					}
				} elseif ($context) {
					// one container context
					$html = $this->backend_container_wrapper($html, $context);
				}
			}
		}
		return $html;
	}
	private function backend_container_wrapper($html, $context=false)
	{
		$html = $this->backend_wrapper($html, 'container', $context);
		return $html;
	}
	private function backend_wrapper($html='', $type='model', $context=false)
	{
		if ($context) {
			$backend_type_context = $context;
		} elseif (is_callable($this->backend_context[$type])) {
			$backend_type_context = $this->backend_context[$type]($this);
		} else {
			$backend_type_context = $this->backend_context[$type];
		}
		if (!($tree = \runner::config("tree"))) {
			$scaffold = $this->scaffold_root;
			$tree = (@include $scaffold . '/model/tree.php');
		}
		if ($type == 'container') {
			// get parent model
			$parent = 0;
			if (isset($backend_type_context['parent'])) {
				$parent = $backend_type_context['parent'];
			}
			if (!$parent && $this->model && $this->model->reference
				&& ($parents = \Routerunner\Bootstrap::parent($this->model->reference))) {
				$array_pop = array_pop($parents);
				$parent = $array_pop['reference'];
			}
			if (!$parent && isset($backend_type_context['traverse'])
				&& is_array($traverse = $backend_type_context['traverse'])) {
				if (count($traverse) && ($lvl = array_pop($traverse))
					&& ($parent_table_id = \bootstrap::get($lvl))) {
					if (is_array($parent_table_id)) {
						$tmp_parent_table_id = false;
						foreach ($parent_table_id["parents"] as $tmp_parent) {
							if ($tmp_parent["model_class"] == $lvl) {
								$tmp_parent_table_id = $tmp_parent["reference"];
							}
						}
						if ($tmp_parent_table_id) {
							$parent_table_id = $tmp_parent_table_id;
						}
					}
					$SQL = 'SELECT reference FROM {PREFIX}models WHERE model_class = :class AND table_id = :table_id';
					if ($result = \db::query($SQL, array(':class' => $lvl, ':table_id' => $parent_table_id))) {
						$parent = $result[0]['reference'];
					}
				}
			}

			// get acceptable models
			$accept = array();
			if (isset($backend_type_context['accept'])) {
				$accept = $backend_type_context['accept'];
			} elseif (isset($backend_type_context['traverse']) &&
				($branch = \Routerunner\Helper::tree_route($tree, $backend_type_context['traverse'], $this->model))) {
				foreach ($branch['children'] as $child_class => $child_params) {
					if (isset($child_params['blank'])) {
						$blank[] = $child_class;
						$accept[$child_class] = $child_params['blank'];
					}
				}
			}

			$backend_type_context['parent'] = $parent;
			$backend_type_context['blank'] = $accept;
			if (!isset($backend_type_context['wrapper'])) {
				$backend_type_context['wrapper'] = array(
					'class' => '',
				);
			} elseif (!isset($backend_type_context['wrapper']['class'])) {
				$backend_type_context['wrapper']['class'] = '';
			}
			$classes = explode(' ', $backend_type_context['wrapper']['class']);
			$classes = array_merge($classes, array_keys($accept));
			$backend_type_context['wrapper']['class'] .= implode(' ', $classes);
		}

		$dom = false;
		if ($html) {
			if (isset($backend_type_context['template']['opening'])) {
				$dom = \phpQuery::newDocument($backend_type_context['template']['opening'] . $html .
					$backend_type_context['template']['closing']);
				if (!$dom) {
					$dom = \phpQuery::newDocumentHTML($html);
				}
			} elseif (isset($backend_type_context['wrapper']['element'])) {
				$dom = \phpQuery::newDocumentHTML($html, $charset = 'utf-8');

				$dom = $this->dom_wrapper($dom, $backend_type_context['wrapper']['element'], null);
			} else {
				$dom = \phpQuery::newDocumentHTML($html);
			}
		} elseif (isset($backend_type_context['wrapper']['element'])) {
			$html = '<' . $backend_type_context['wrapper']['element'] . '></' .
				$backend_type_context['wrapper']['element'] . '>';
			$dom = \phpQuery::newDocumentHTML($html);
		} elseif (isset($backend_type_context['template']['opening'])) {
			$html = $backend_type_context['template']['opening'] .
				$backend_type_context['template']['closing'];
			$dom = \phpQuery::newDocumentHTML($html);
		}

		$root_nodes = array();
		if ($dom && isset($backend_type_context['selector'])) {
			$root_nodes = pq($backend_type_context['selector'] . ":not(.routerunner-" . $type . ")");
		} elseif ($dom) {
			$root_nodes = $dom->children(":not(.routerunner-" . $type . ")");
		}

		$model = false;
		if ($type == 'model' && isset($this->model)) {
			$model = $this->model;
		} elseif ($type == 'container' && isset($backend_type_context['blank'])) {
			$model = new \stdClass();
			$model->reference = 0;
			foreach ($backend_type_context['blank'] as $blank_field => $blank_value) {
				$model->$blank_field = $blank_value;
			}
		}

		foreach ($root_nodes as $index => $node) {
			if (!in_array(strtolower($node->tagName), array("html", "head", "body", "script", "style"))) {
				if (!$this->unique) {
					$this->unique = uniqid();
				}

				$backend_class = 'routerunner-backend routerunner-inline routerunner-' . $type .
					' routerunner-' . $type . '-wrapper ';
				if (isset($backend_type_context['wrapper']['class'])) {
					$backend_class .= $backend_type_context['wrapper']['class'] . ' ';
				}

				$pqnode = pq($node);

				$backend_context = $backend_type_context;
				unset($backend_context['wrapper']['element'], $backend_context['wrapper']['class'],
					$backend_context['wrapper']['attr'], $backend_context['template']['opening'],
					$backend_context['template']['closing']);

				$pqnode->removeAttr('data-routerunner-id');
				$pqnode->removeAttr('data-route');
				$pqnode->removeAttr('data-table_id');
				if ($model) {
					foreach ($model as $data => $value) {
						$pqnode->removeAttr('data-' . $data);
					}
				}
				if (isset($backend_type_context['wrapper']['attr'])
					&& is_array($backend_type_context['wrapper']['attr'])
				) {
					foreach (array_keys($backend_type_context['wrapper']['attr']) as $attr) {
						$pqnode->removeAttr($attr);
					}
				}
				if (isset($backend_context) && is_array($backend_context)) {
					foreach (array_keys($backend_context) as $data) {
						$pqnode->removeAttr('data-' . $data);
					}
				}

				// modify model tag
				if ($type == 'model' && $model) {
					$this->backend_id[$index] = 'ref' . $model->reference . '_' . $this->unique;
					$model->backend_ref = $this->backend_id[$index];
					$pqnode->attr('data-routerunner-id', $this->backend_id[$index]);
					$pqnode->attr('data-route', $this->router->runner->path . $this->router->runner->route);
					$pqnode->attr('data-url', $model->url());
					if ($model) {
						foreach ($model as $data => $value) {
							if (is_array($value)) {
								$value = json_encode($value, JSON_HEX_APOS);
							} else {
								$value = htmlentities(addslashes(preg_replace('/\n|\r/m', '', $value)));
							}
							$pqnode->attr('data-' . $data, $value);
						}
					}
				} elseif ($type == 'container') {
					$this->backend_id[$index] = 'route_' . trim(str_replace('/', '_', $this->path . $this->route), '_')
						. '_' . $this->unique;
					$pqnode->attr('data-routerunner-id', $this->backend_id[$index]);
					$pqnode->attr('data-route', $this->router->runner->path . $this->router->runner->route);
					if ($model) {
						foreach ($model as $attr => $value) {
							$traverse_child = (isset($backend_type_context['traverse'])
								? $backend_type_context['traverse'] : array());
							$traverse_child[] = $attr;
							if ($branch = \Routerunner\Helper::tree_route($tree, $traverse_child)) {
								foreach ($branch as $branch_attr => $branch_value) {
									if (substr($branch_attr, 0, 4) == 'btn-' || $branch_attr == 'icon') {
										$value[$branch_attr] = $branch_value;
									}
								}
							}

							if (is_array($value)) {
								$value = json_encode($value, JSON_HEX_APOS);
							} else {
								$value = htmlentities(addslashes(preg_replace('/\n|\r/m', '', $value)));
							}
							$pqnode->attr('data-' . str_replace("/", "-", $attr), $value);
						}
					}
				}
				if (isset($backend_type_context['wrapper']['attr'])
					&& is_array($backend_type_context['wrapper']['attr'])
				) {
					foreach ($backend_type_context['wrapper']['attr'] as $attr => $value) {
						$pqnode->attr($attr, $value);
					}
				}
				$backend_classes = explode(' ', $backend_class);
				foreach ($backend_classes as $class) {
					if (!$pqnode->hasClass($class)) {
						$pqnode->addClass($class);
					}
				}

				if (isset($backend_context) && is_array($backend_context)) {
					foreach ($backend_context as $data => $value) {
						if (is_array($value)) {
							$value = json_encode($value, JSON_HEX_APOS);
						} else {
							$value = htmlentities(addslashes(preg_replace('/\n|\r/m', '', $value)));
						}

						$pqnode->attr('data-' . $data, $value);
					}
				}
			}
		}

		$return = "";
		if ($dom) {
			$return = $dom->htmlOuter();
		}
		return $return;
	}

	private function dom_wrapper($dom, $wrapper_html, $element_selector=false) {
		if ($element_selector) {
			foreach ($dom[$element_selector] as $node) {
				pq($node)->wrap('<' . $wrapper_html . '></' . $wrapper_html . '>');
			}
		} else {
			foreach ($dom->children() as $node) {
				pq($node)->wrap('<' . $wrapper_html . '></' . $wrapper_html . '>');
			}
		}
		return $dom;
	}
}
