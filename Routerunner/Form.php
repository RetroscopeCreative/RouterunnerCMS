<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.12.05.
 * Time: 13:23
 */

namespace Routerunner;

class Form
{
	private $runner = false;
	private $path = '';
	private $formname = '';
	private $fields = array();
	private $fid = '';
	protected $class = '';
	public $view = '';
	public $id_field = '_routerunner_form_id';

	private $nonce = false;

	public static $forms = array();
	public static $id = false;

	public function __construct($runner, $formname, $params, & $repost_form_after_submit=false)
	{
		$this->runner = $runner;
		$this->fid = str_replace('.', '_', uniqid('', true));
		$this->path = $runner->path . $runner->route;
		$this->formname = $formname;
		$this->id_field .= str_replace('/', '_', $this->path . '/' . $this->formname);

		if (\Routerunner\Routerunner::$slim->request) {
			$request_params = \Routerunner\Routerunner::$slim->request->params();
			if (!empty($request_params[$this->id_field])) {
				$this->fid = $request_params[$this->id_field];
			}

			$form_method = (($repost_form_after_submit && ($repost_form_after_submit === 'put'
					|| $repost_form_after_submit === 'get' || $repost_form_after_submit === 'post'
					|| $repost_form_after_submit === 'delete'))
				? $repost_form_after_submit : \Routerunner\Routerunner::$static->request);
			switch ($form_method) {
				case "put":
				case "post":
				case "delete":
					$method = ($request_params
						&& (isset($request_params[$formname])
							|| (isset($request_params["submit"]) && isset($params["input"]["submit"]["value"])
								&& $request_params["submit"] == $params["input"]["submit"]["value"]))) ? 'submit' : 'form';
					break;
				default:
					$method = 'form';
			}
			$repost_form_after_submit = $method;
			$this->view = trim($runner->route, '\\') . '.' . $formname . '.' . $method . '.php';
			if (!file_exists($runner->router->scaffold_root . DIRECTORY_SEPARATOR . $this->path . DIRECTORY_SEPARATOR . $this->view)) {
				$method = 'form';
				$this->view = trim($runner->route, '\\') . '.' . $formname . '.' . $method . '.php';
			}

			$flash = $params['form'];
			$flash['fields'] = array_keys($params['input']);

			if ($method == 'form') {
				$this->nonce = uniqid(rand(0, 1000000));
				$_SESSION["nonce-" . $this->fid] = \Routerunner\Crypt::crypter($this->nonce);
				\Routerunner\Routerunner::$slim->flash($this->path . DIRECTORY_SEPARATOR . $formname, $flash);
			}

			$this->params = $params["form"];
			$this->fields = $params["input"];

			$this->fields[$this->id_field] = array(
				'type' => 'hidden',
				'field' => $this->id_field,
				'input-id' => '_routerunner_form_id',
				'value' => $this->fid,
			);
			$this->fields['_routerunner_form_nonce'] = array(
				'type' => 'hidden',
				'field' => '_routerunner_form_nonce',
				'input-id' => '_routerunner_form_nonce',
				'value' => $this->nonce,
			);

			if (isset($params["unset"])) {
				$this->unset = $params["unset"];
			}
			if (isset($params["set"])) {
				$this->set = $params["set"];
			}
			if ($runner->model) {
				if (is_array($runner->model) && $runner->model) {
					$this->class = get_class(current($runner->model));
				} elseif (is_object($runner->model)) {
					$this->class = get_class($runner->model);
				}
				if ($this->class) {
					$this->class = trim(substr($this->class, strrpos($this->class, DIRECTORY_SEPARATOR)), DIRECTORY_SEPARATOR);
				}
			}

			foreach ($this->fields as $field_name => & $field_param) {
				if ((!isset($field_param['value']) || !$field_param['value']) && isset($runner->model->$field_name))
					$field_param['value'] = $runner->model->$field_name;
				if ((!isset($field_param['value']) || !$field_param['value']) && isset($runner->context[$field_name]))
					$field_param['value'] = $runner->context[$field_name];
				if ((!isset($field_param['value']) || !$field_param['value']) && isset($request_params[$field_name]))
					$field_param['value'] = $request_params[$field_name];
			}

			$runner->form[$formname] = $this;

			\Routerunner\Form::$forms[$formname] = $this;
		}
	}

	public function render($runner)
	{
		\Routerunner\Form::$id = 'frm_' . $this->fid;
		return $runner->form($this->formname);
	}

	public function field($field, $value=null, $overwrite=array())
	{
		if (!isset($this->fields[$field])) {
			return '';
		}
		$field_params = $this->fields[$field];
		$fieldname = (isset($overwrite['name']) ? $overwrite['name'] : $field);
		$field_params = array_merge($field_params, $overwrite);

		$scaffold_root = (isset($this->runner->router->scaffold_root)
			? $this->runner->router->scaffold_root : \Routerunner\Helper::$scaffold_root);
		$path = $scaffold_root . $this->path . DIRECTORY_SEPARATOR;
		$input_root = $scaffold_root . DIRECTORY_SEPARATOR . 'input' . DIRECTORY_SEPARATOR;
		$input_path = false;
		if (file_exists($path . 'input.' . $this->formname . '.' . $field . '.php')) {
			$input_path = $path . 'input.' . $this->formname . '.' . $field . '.php';

		} elseif (file_exists($path . 'input.' . $field . '.php')) {
			$input_path = $path . 'input.' . $field . '.php';

		} elseif (isset($field_params['type']) && file_exists($path . 'input.' . $this->formname . '.' . $field_params['type'] . '.php')) {
			$input_path = $path . 'input.' . $this->formname . '.' . $field_params['type'] . '.php';

		} elseif (isset($field_params['type']) && file_exists($path . 'input.' . $field_params['type'] . '.php')) {
			$input_path = $path . 'input.' . $field_params['type'] . '.php';

		} elseif (file_exists($input_root . $this->formname . '.' . $field . '.php')) {
			$input_path = $input_root . $this->formname . '.' . $field . '.php';

		} elseif (isset($field_params['type']) && file_exists($input_root . $this->formname . '.' . $field_params['type'] . '.php')) {
			$input_path = $input_root . $this->formname . '.' . $field_params['type'] . '.php';

		} elseif (file_exists($input_root . $field . '.php')) {
			$input_path = $input_root . $field . '.php';

		} elseif (isset($field_params['type']) && file_exists($input_root . $field_params['type'] . '.php')) {
			$input_path = $input_root . $field_params['type'] . '.php';

		}

		$return = '';
		if ($input_path) {
			$field_params["id"] = $this->fid . '_' . $field;
			$field_params["name"] = (isset($field_params['type']) && $field_params['type'] == 'submit')
				? $this->formname . '[' . $fieldname . ']' : $fieldname;

			if (isset($field_params['disabled']) && $field_params['disabled'] === true)
				$field_params['disabled'] = ' disabled="disabled" ';

			$field_params['data'] = json_encode($field_params);

			\input::set($field_params);
			ob_start();
			include $input_path;
			$return = ob_get_clean();
		} else {
			$field_id = (isset($field_params['input-id']) ? $field_params['input-id'] : $fieldname);
			$val = (!is_null($value) ? $value : (isset($field_params['value']) ? $field_params['value'] : ''));
			$return = '<input type="hidden" name="'.$fieldname.'" id="'.$field_id.'" value="'.$val.'" />';
		}
		return $return . PHP_EOL;
	}

	public function value($field)
	{
		$ret = null;
		if (isset($this->fields[$field])) {
			$ret = $this->fields[$field]["value"];
		}
		return $ret;
	}

	public static function get($formname)
	{
		$frm = (isset(self::$forms[$formname])) ? self::$forms[$formname] : false;
		if ($frm) {
			\Routerunner\Form::$id = $frm->id;
		}
		return $frm;
	}

	public static function submit($forms, & $errors=array(), & $return_SQL=false, & $return_params=false,
								  & $values=array())
	{
		if (!is_array($forms)) {
			$forms = array($forms);
		}
		foreach ($forms as $frm_name => $form) {
			$flashed = \Routerunner\Routerunner::$slim->flash($form->path . DIRECTORY_SEPARATOR . $form->formname);

			$params = \Routerunner\Bootstrap::$params;

			$halt = false;
			if (isset($flashed, $flashed['fields'])) {
				// check form fields
				$fields = $flashed['fields'];
				$form_fields = array_keys($form->fields);
				if (($_routerunner_form_id_index = array_search($form->id_field, $form_fields)) &&
					($_routerunner_form_nonce_index = array_search('_routerunner_form_nonce', $form_fields))) {
					unset($form_fields[$_routerunner_form_id_index], $form_fields[$_routerunner_form_nonce_index]);
				}

				if (\Routerunner\Common::arrDiff($fields, $form_fields)) {
					// exception
					$halt = true;
				}
				unset($flashed['fields']);

				// check form params
				/*
				if (\Routerunner\Common::arrDiff($flashed, $form->params)) {
					// exception
					$halt = true;
				}
				*/
				$form->params = $flashed;
			} else {
				$errors[] = 'Form not exists or the page has been refreshed!';
			}


			$fid = false;
			if (!empty($form->fields[$form->id_field]['value'])) {
				$fid = $form->fields[$form->id_field]['value'];
			}
			if ($fid && !empty($form->fields['_routerunner_form_nonce']['value'])) {
				if (!isset($_SESSION['nonce-' . $fid]) ||
					!\Routerunner\Crypt::checker($form->fields['_routerunner_form_nonce']['value'], $_SESSION['nonce-' . $fid])) {
					$errors[] = 'Error in form submit or data has been sent already!';
					$halt = true;
				}
			}
			if (!$halt) {
				unset($form->fields[$form->id_field]);
				unset($form->fields['_routerunner_form_nonce']);
				unset($_SESSION['nonce-' . $fid]);
			}

			$succeed = false;
			if (!$halt) {
				$error_row = (isset($form->params['error_format']))
					? $form->params['error_format'] : '<p class="err">%s</p>'.PHP_EOL;

				$succeed = true;
				$submit_params = array();
				if (isset($form->unset) && is_array($form->unset)) {
					foreach ($form->unset as $field) {
						if (isset($form->fields[$field], $form->fields[$field]["value"])) {
							$values[$field] = $form->fields[$field]["value"];
						} elseif (isset($form->fields[$field])) {
							$values[$field] = $form->fields[$field]["value"];
						}
						unset($form->fields[$field]);
					}
				}
				if (isset($form->set) && is_array($form->set)) {
					foreach ($form->set as $field => $value) {
						$values[$field] = $value;
						$form->fields[$field] = array("field" => $field, "value" => $value);
					}
				}
				foreach ($form->fields as $field => $field_param)
				{
					$field_succeed = true;

					$values[$field] = null;
					if (!isset($params[$field]) && isset($field_param['value'])) {
						$params[$field] = $field_param['value'];
					}

					$regexps = (isset($field_param['regexp'])) ? $field_param['regexp'] : false;
					if ($regexps && !is_array($regexps)) {
						$regexps = array($regexps);
					} elseif (!$regexps) {
						$regexps = array();
					}
					if (!isset($params[$field]) || !$params[$field]) {
						if (isset($field_param['default_on_fail'], $field_param['default'])
							&& $field_param['default_on_fail']) {
							$params[$field] = $field_param['default'];
						} elseif (isset($field_param['errormsg'])) {
							$errors[$field] = sprintf($error_row, $field_param['errormsg']);
							if (isset($field_param['mandatory']) && $field_param['mandatory']["value"] === true) {
								if (isset($field_param['mandatory']['msg']) && !isset($errors[$field])) {
									$errors[$field] = sprintf($error_row, $field_param['mandatory']['msg']);
								}
								$field_succeed = false;
								$regexps = array();
							}
						} elseif (isset($field_param['mandatory']) && $field_param['mandatory']["value"] === true) {
							if (isset($field_param['mandatory']['msg']) && !isset($errors[$field])) {
								$errors[$field] = sprintf($error_row, $field_param['mandatory']['msg']);
							}
							$field_succeed = false;
							$regexps = array();
						}
					}
					foreach ($regexps as $regexp) {
						$isOk = false;
						if (is_array($regexp["value"])) {
							foreach ($regexp["value"] as $regexp_key => $regexp_value) {
								$pattern = "~" . trim($regexp_value, "/~ ") . "~";
								if (isset($regexp['options'])) {
									$pattern .= (is_array($regexp["options"]) && isset($regexp["options"][$regexp_key])
										? $regexp["options"][$regexp_key] : $regexp["options"]);
								}
								if (preg_match($pattern, $params[$field])) {
									$isOk = true;
								}
							}
						} else {
							$pattern = "~" . trim($regexp["value"], "~/ ") . "~";
							if (isset($regexp['options'])) {
								$pattern .= $regexp['options'];
							}
							$isOk = preg_match($pattern, $params[$field]);
						}
						if (isset($params[$field]) && !$isOk) {
							if (isset($regexp['msg']) && !isset($errors[$field])) {
								$errors[$field] = sprintf($error_row, $regexp['msg']);
							}
							$field_succeed = false;
						}
					}

					if ($field_succeed) {
						if (isset($params[$field]) && isset($field_param["field"])) {
							if (isset($field_param['function']) && function_exists($field_param['function'])) {
								$fn = $field_param['function'];
								$submit_params[$field] = $fn($params[$field]);
							} else {
								$submit_params[$field] = $params[$field];
							}
							$values[$field] = $submit_params[$field];
						}
					} else {
						$succeed = false;
					}
				}
			}

			if ($succeed) {
				$method = (isset($form->params['xmethod'])) ? $form->params['xmethod'] : $form->params['method'];
				if (isset($form->params[$method.'_sql'])) {
					$sql = $form->params[$method.'_sql'];
					if (preg_match('/\:[a-z0-9]+/im', $sql)) {
						// named parameters
						array_walk($sql_params, function($value, & $key) {
							if (substr($key, 0, 1) != ':')
								$key = ':' . $key;
						});
					}
				} else {
					$from = (isset($form->params['from'])) ? $form->params['from'] : $form->class;
					$from = \Routerunner\Common::dbField($from);

					$sql_params = array();
					if ($method === 'post') {
						$sql = 'INSERT INTO ' . $from . ' (';
						$fields = array();
						foreach ($submit_params as $field => $submit_value)
						{
							$field_param = $form->fields[$field];
							if (isset($params[$field]) && (!isset($field_param['fixed']) || $field_param['fixed'] !== true)
								&& (!isset($field_param['field']) || $field_param['field'] !== false)) {
								$_field = (isset($field_param['field'])) ? $field_param['field'] : $field;
								$fields[] = \Routerunner\Common::dbField($_field);

								$param_key = \Routerunner\Common::dbField($_field, ':', '', '.', '` .', '.');
								$sql_params[$param_key] = $submit_value;
								/*
								if (isset($submit_params[$field])) {
									$sql_params[$param_key] = $submit_params[$field];
								} else {
									$sql_params[$param_key] = $params[$field];
								}
								*/
							}
						}
						$sql .= implode(', ', $fields) . ') VALUES (' . implode(', ', array_keys($sql_params)) . ')';
					} elseif ($method == 'put') {
						$sql = 'UPDATE ' . $from . ' SET ';
						$fields = array();
						foreach ($submit_params as $field => $submit_value)
						{
							$field_param = $form->fields[$field];
							if (isset($params[$field]) && (!isset($field_param['fixed']) || $field_param['fixed'] !== true)
								&& (!isset($field_param['field']) || $field_param['field'] !== false)) {
								$_field = (isset($field_param['field'])) ? $field_param['field'] : $field;
								$row = \Routerunner\Common::dbField($_field) . ' = ';
								$param_key = \Routerunner\Common::dbField($_field, ':', '', '.', '` .', '.');
								$row .= $param_key;
								$sql_params[$param_key] = $submit_value;
								/*
								if (isset($submit_params[$field])) {
									$sql_params[$param_key] = $submit_params[$field];
								} else {
									$sql_params[$param_key] = $params[$field];
								}
								*/
								$fields[] = $row;
							}
						}
						$sql .= implode(', ', $fields) . ' WHERE ';
						if (isset($form->params['condition'])) {
							$conditions = $form->params['condition'];
							while ($condition = array_shift($conditions)) {
								if (!is_array($condition))
									$condition = array($condition);

								$add_condition = true;
								if (isset($condition[1]) && is_array($condition[1])) {
									foreach ($condition[1] as $condition_field => $condition_value) {
										if (isset($form->fields[$condition_value]['value'])) {
											$sql_params[$condition_field] = $form->fields[$condition_value]['value'];
										} else {
											$add_condition = false;
										}
									}
								} elseif (isset($condition[1])) {
									$sql_params[] = $condition[1];
								} else {
									$add_condition = false;
								}
								if ($add_condition) {
									$sql .= $condition[0];
									if (count($conditions) && isset($condition[2])) {
										$sql .= ' ' . $condition[2] . ' ';
									}
								}
							}
						} else {
							// exception
						}
					} elseif ($method == 'delete') {
						$sql = 'DELETE FROM ' . $from . ' WHERE ';
						if (isset($form->params['condition'])) {
							$conditions = $form->params['condition'];
							while ($condition = array_shift($conditions)) {
								if (!is_array($condition))
									$condition = array($condition);

								$add_condition = true;
								if (isset($condition[1]) && is_array($condition[1])) {
									foreach ($condition[1] as $condition_field => $condition_value) {
										if (isset($form->fields[$condition_value]['value'])) {
											$sql_params[$condition_field] = $form->fields[$condition_value]['value'];
										} else {
											$add_condition = false;
										}
									}
								} elseif (isset($condition[1])) {
									$sql_params[] = $condition[1];
								} else {
									$add_condition = false;
								}
								if ($add_condition) {
									$sql .= $condition[0];
									if (count($conditions) && isset($condition[2])) {
										$sql .= ' ' . $condition[2] . ' ';
									}
								}
							}
						} elseif (isset($submit_params) && $submit_params) {
							$fields = array();
							foreach ($submit_params as $field => $submit_value)
							{
								$field_param = $form->fields[$field];
								if (isset($params[$field]) && (!isset($field_param['fixed']) || $field_param['fixed'] !== true)
									&& (!isset($field_param['field']) || $field_param['field'] !== false)) {
									$_field = (isset($field_param['field'])) ? $field_param['field'] : $field;
									$row = \Routerunner\Common::dbField($_field) . ' = ';
									$param_key = \Routerunner\Common::dbField($_field, ':', '', '.', '` .', '.');
									$row .= $param_key;
									$sql_params[$param_key] = $submit_value;
									/*
									if (isset($submit_params[$field])) {
										$sql_params[$param_key] = $submit_params[$field];
									} else {
										$sql_params[$param_key] = $params[$field];
									}
									*/
									$fields[] = $row;
								}
							}
							$sql .= implode(' AND ', $fields);
						} else {
							// exception
						}
					}
				}
				if ($return_SQL || $return_params) {
					$return_SQL = $sql;
					$return_params = $sql_params;
				} else {
					\Routerunner\Db::begin_transaction();
					if ($method === 'post') {
						$succeed = \Routerunner\Db::insert($sql, $sql_params);
					} else {
						\Routerunner\Db::query($sql, $sql_params);
					}
					\Routerunner\Db::commit();
				}
			}
		}
		return $succeed;
	}
}