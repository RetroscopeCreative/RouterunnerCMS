<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.12.03.
 * Time: 16:00
 */
namespace Routerunner;

class BaseModel
{
	public $class = __CLASS__;
	public $reference = false;
	public $route = false;
	public $table_from = false;
	public $table_id = false;
	public $permission = false;
	public $states = array(
		'active' => 1,
		'begin' => null,
		'end' => null,
		'params' => null,
	);
	public $override = array();

	public function __construct($route='/', $model_data=array(), $from=null, $id=null, $override=array(), $model_context=array())
	{
		$this->route = $route;
		$this->override = $override;
		$this->init($model_data, $from, $id, $model_context);
	}
	protected function init($model_data=array(), $from=null, $id=null, $model_context=array()) {
		$this->class = trim(substr(get_class($this), strrpos(get_class($this), '\\')), '\\');

		$this->referencing($from, $id, $model_context);

		if ($this->readable()) {
			$this->set($model_data);
		}
	}
	public function set($model_data=array())
	{
		if (is_array($model_data)) {
			unset($model_data["class"], $model_data["reference"], $model_data["route"],
				$model_data["table_from"], $model_data["table_id"]);
		} elseif (is_object($model_data)) {
			unset($model_data->class, $model_data->reference, $model_data->route,
				$model_data->table_from, $model_data->table_id);
		}
		foreach ($model_data as $var => $value) {
			if (property_exists(get_class($this), $var))
				$this->$var = $value;
		}
	}

	public function referencing($from=null, $id=null, $model_context=array())
	{
		if ($reference = self::resolve_model_reference('self', $model_context['where'], true)) {
			if (is_array($reference) && count($reference) == 1) {
				$this->reference = $reference[0];
			}
		} elseif ($reference = self::resolve_model_reference('direct', $model_context['where'], true)) {
			if (is_array($reference) && count($reference) == 1) {
				$this->reference = $reference[0];
			}
		}

		if (!is_null($from) && !is_null($id) && is_numeric($id)) {
			$SQL = 'SELECT `models`.`reference` FROM `{PREFIX}models` AS `models` WHERE `models`.`model_class` = :class' .
				PHP_EOL;
			$params = array(':class' => $this->class);
			if ($this->class != $from) {
				$SQL .= 'AND `models`.`table_from` = :from' . PHP_EOL;
				$params[':from'] = $from;
			}
			$SQL .= 'AND `models`.`table_id` = :id';
			$params[':id'] = $id;

			if ($result = \Routerunner\Db::query($SQL, $params)) {
				$this->reference = $result[0]['reference'];
			} elseif (substr(get_class($this), 0, 8) != 'backend\\') {
				// create reference
				$SQL = 'INSERT INTO `{PREFIX}models` (`model_class`, `table_from`, `table_id`) VALUES (?, ?, ?)';
				$params = array(
					$this->class,
					$from,
					$id,
				);
				$this->created = true;
				$this->reference = \Routerunner\Db::insert($SQL, $params);
			}
		} elseif ($this->reference) {
			$SQL = 'SELECT `models`.`table_from`, `models`.`table_id` FROM `{PREFIX}models` AS `models` WHERE '.
				'`models`.`reference` = :reference' . PHP_EOL;
			$params = array(':reference' => $this->reference);
			if ($result = \Routerunner\Db::query($SQL, $params)) {
				$from = $result[0]["table_from"];
				$id = $result[0]["table_id"];
			}
		}

		$this->table_from = $from;
		$this->table_id = $id;

		$parents = \runner::stack("parents");
		if (isset($parents[$this->reference])) {
			$this->parent = $parents[$this->reference];
		}
		$prevs = \runner::stack("prevs");
		if (isset($prevs[$this->reference])) {
			$this->prev = $prevs[$this->reference];
		}

		if (!isset($model_context['skip_referencing']) || !$model_context['skip_referencing']) {
			$this->statement();
			$this->permissioning();
		}
	}

	public function readable()
	{
		return ((!is_array($this->permission) || current($this->permission) & PERM_READ) ? true : false);
	}
	public function writable()
	{
		return ((is_array($this->permission) && current($this->permission) & PERM_UPDATE) ? true : false);
	}
	public function creatable()
	{
		return ((is_array($this->permission) && current($this->permission) & PERM_CREATE) ? true : false);
	}
	public function deletable()
	{
		return ((is_array($this->permission) && current($this->permission) & PERM_DELETE) ? true : false);
	}
	public function activate_allowed()
	{
		return ((is_array($this->permission) && current($this->permission) & PERM_ACTIVE) ? true : false);
	}
	public function movable()
	{
		return ((is_array($this->permission) && current($this->permission) & PERM_MOVE) ? true : false);
	}
	public function visible()
	{
		if (!is_null($this->states['params'])) {
			return false;
		} elseif (!is_null($this->states['begin']) || !is_null($this->states['end'])) {
			return (!is_null($this->states['begin']) && $this->states['begin'] > time())
			|| (!is_null($this->states['end']) && $this->states['end'] < time()) ? false : true;
		} else {
			return $this->states['active'] ? true : false;
		}
	}

	public function url($force_class=false,
						& $reference=null, & $params=array(), & $keywords='', & $description='', & $meta='')
	{
		$url = '';
		$return = false;

		$SQL = <<<SQL
SELECT `url`, `reference`, `params`
FROM `{PREFIX}rewrites`
WHERE `reference` = :reference
ORDER BY `primary` DESC, `rewrite_id`
LIMIT 1

SQL;
		$subdir = '';
		$subdirs = \runner::config('subdir');

		if ($result = \db::query($SQL, array(':reference' => $this->reference))) {
			$result = array_shift($result);
			$reference = $result['reference'];
			$params = $result['params'];
			$url = $subdir . $result['url'];
		} elseif (!isset($this->table_id)) {
			$return = $subdir . 'ref/' . $this->reference;
		} else {
			$return  = $subdir . ($force_class ? $force_class : $this->class) . '/' . $this->table_id;
		}
		if ($return) {
			$SQL = <<<SQL
SELECT `url`, `reference`, `params`
FROM `{PREFIX}rewrites`
WHERE `resource_uri` = :uri
ORDER BY `primary` DESC, `rewrite_id`
LIMIT 1

SQL;

			if ($result = \db::query($SQL, array(':uri' => $return))) {
				$result = array_shift($result);
				$reference = $result['reference'];
				$params = $result['params'];
				$url = $subdir . $result['url'];
			}
		}
		if (!$url && $return) {
			$url = $return;
		}
		return $url;
	}

	public function statement($session=false)
	{
		$SQL = 'SELECT `active`, `begin`, `end`, `params` FROM `{PREFIX}model_states` WHERE `model` = :reference';
		if ($this->reference && ($result = \Routerunner\Db::query($SQL, array(':reference' => $this->reference)))) {
			$this->states = $result[0];
		}
		if (\runner::config('mode') == 'backend' && $this->reference) {
			$result = false;
			$input = array(
				"session" => ($session ? $session : \runner::stack("session_id")),
				"reference" => $this->reference,
				"draft" => true,
				"state" => "visibility"
			);
			if (($changes = $this->changes($input, $result)) && isset($changes[$this->reference]["state"]) &&
				is_array($changes[$this->reference]["state"])) {
				$this->states = array_merge($this->states, $changes[$this->reference]["state"]);
			}
		}
		$this->states["active"] = filter_var($this->states["active"], FILTER_VALIDATE_BOOLEAN);
	}

	public function permissioning()
	{
		$runner = \rr::instance();
		$permissions = (isset($runner->permissions) ? $runner->permissions : false);
		if ($permissions && is_array($permissions)) {
			while ($permission = array_shift($permissions)) {
				if (isset($permission['owner'])) {
					if (isset($this->owner) && !is_array($this->owner)) {
						$this->owner = array($this->owner);
						$this->owner[] = $permission['owner'];
					} else {
						$this->owner = $permission['owner'];
					}
				}
				if (isset($permission['group'])) {
					if (isset($this->group) && !is_array($this->group)) {
						$this->group = array($this->group);
						$this->group[] = $permission['group'];
					} else {
						$this->group = $permission['group'];
					}
				}
				if (isset($permission['other']))
					$this->other = $permission['other'];

				//if (isset($permission['owner'])
				//	&& (int) $permission['owner'] === (int) \Routerunner\Routerunner::get('uid')) {
				$email = null;
				$name = null;
				$group = null;
				if ($me = \user::me($email, $name, $group)) {
					if (isset($permission['owner'])
						&& (int) $permission['owner'] === (int) $me) {
						$this->permission = array('owner' => $permission['permission']);
					}
					//if (isset($permission['group']) &&
					//	($this->permission === false || (is_array($this->permission) && key($this->permission) == 'other'))
					//	&& (int) $permission['group'] === (int) \Routerunner\Routerunner::get('gid')) {
					if (isset($permission['group']) &&
						($this->permission === false || (is_array($this->permission) && key($this->permission) == 'other'))
						&& (int) $permission['group'] === (int) $group) {
						$this->permission = array('group' => $permission['permission']);
					}
				}
				if (isset($permission['other']) && $this->permission === false && $permission['other'] === '1') {
					$this->permission = array('other' => $permission['permission']);
				}
			}
		} else {
			$SQL = 'SELECT `label`, `owner`, `group`, `other`, `permission` FROM `{PREFIX}permissions` AS `permissions`' .
				PHP_EOL;
			$SQL .= 'WHERE `permissions`.`reference` = ?' . PHP_EOL;
			$SQL .= 'ORDER BY CASE WHEN `other` = 1 THEN 2 WHEN `group` IS NOT NULL THEN 1 ' . PHP_EOL;
			$SQL .= 'WHEN `owner` IS NOT NULL THEN 0 ELSE 4 END, `permission_id`';
			if ($result = \Routerunner\Db::query($SQL, array($this->reference))) {
				while ($permission = array_shift($result)) {
					if (!is_null($permission['owner'])) {
						if (isset($this->owner) && !is_array($this->owner)) {
							$this->owner = array($this->owner);
							$this->owner[] = $permission['owner'];
						} else {
							$this->owner = $permission['owner'];
						}
					}
					if (!is_null($permission['group'])) {
						if (isset($this->group) && !is_array($this->group)) {
							$this->group = array($this->group);
							$this->group[] = $permission['group'];
						} else {
							$this->group = $permission['group'];
						}
					}
					if (!is_null($permission['other']))
						$this->other = $permission['other'];


					if (!is_null($permission['owner'])
						&& (int) $permission['owner'] === (int) \Routerunner\Routerunner::get('uid')) {
						$this->permission = array('owner' => $permission['permission']);
					}
					if (($this->permission === false || (is_array($this->permission) && key($this->permission) == 'other'))
						&& (int) $permission['group'] === (int) \Routerunner\Routerunner::get('gid')) {
						$this->permission = array('group' => $permission['permission']);
					}
					if ($this->permission === false && $permission['other'] === '1') {
						$this->permission = array('other' => $permission['permission']);
					}
				}
			}
		}
	}

	public function draft($session=false, & $result = false) {
		$input = array(
			"session" => ($session ? $session : \runner::stack("session_id")),
			"reference" => $this->reference,
			"draft" => true
		);
		return $this->changes($input, $result);
	}

	public function history($session=false, & $result = false) {
		$input = array(
			"session" => ($session ? $session : \runner::stack("session_id")),
			"reference" => $this->reference,
			"history" => true
		);
		return $this->changes($input, $result);
	}

	public function changes($input, & $result=false) {
		$changes = array();
		$SQL = <<<SQL
SELECT changes.`session`, changes.`reference`, changes.`changes`, changes.`state`, changes.`date`,
sessions.`user`, users.email, users.name, usergroup.label AS usergroup, users.custom_data
FROM `{PREFIX}changes` AS changes
LEFT JOIN `{PREFIX}sessions` AS sessions ON sessions.session_id = changes.`session`
LEFT JOIN `{PREFIX}user` AS users ON users.user_id = sessions.`user`
LEFT JOIN `{PREFIX}usergroup` AS usergroup ON usergroup.usergroup_id = users.usergroup

SQL;
		$where = array();
		if (isset($input["session"])) {
			$where["changes.session = ?"] = $input["session"];
		}
		if (isset($input["reference"])) {
			$where["changes.reference = ?"] = $input["reference"];
		}
		if (isset($input["change_id"])) {
			$where["changes.change_id = ?"] = $input["change_id"];
		}
		if (isset($input["state"])) {
			$where["changes.state = ?"] = $input["state"];
		}
		if (isset($input["draft"])) {
			$where["changes.approved IS NULL"] = null;
		}
		if (isset($input["history"])) {
			$where["changes.approved IS NOT NULL"] = null;
		}
		if ($where) {
			$SQL .= "WHERE " . implode(" AND ", array_keys($where)) . PHP_EOL;
			$SQL .= "ORDER BY changes.change_id DESC" . PHP_EOL;
			$params = array();
			foreach (array_values($where) as $param_value) {
				if (!is_null($param_value)) {
					$params[] = $param_value;
				}
			}
			if ($result = \db::query($SQL, $params)) {
				foreach ($result as $row) {
					if (!isset($changes[$row["reference"]])) {
						$changes[$row["reference"]] = array();
					}
					$changed = json_decode($row["changes"], true);
					foreach ($changed as $changed_field => $changed_value) {
						if ($row["state"] == "visibility") {
							$changes[$row["reference"]]["state"][$changed_field] = $changed_value;
						} elseif (!isset($changes[$row["reference"]][$changed_field])) {
							$changes[$row["reference"]][$changed_field] = $changed_value;
						}
					}
				}
			}
		}
		return $changes;
	}

	public static function load($context, $model, & $pager=array()) {
		$from = ((isset($context["from"])) ? $context["from"] : $model->class);
		$select = array();
		$predefined = array('route', 'class', 'reference', 'table_from', 'table_id', 'permission', 'permissions',
			'rewrite', 'url', 'override', 'states', 'owner', 'group', 'other', 'parent', 'prev');

		foreach (array_keys(get_object_vars($model)) as $var) {
			if (!in_array($var, $predefined)) {
				$select[$var] = '`' . $var . '`';
			}
		}
		if (isset($context["select"]) && is_array($context["select"])) {
			foreach ($context["select"] as $var => $field) {
				if (isset($select[$var]))
					$select[$var] = $field;
			}
		}
		$leftJoin = ((isset($context["leftJoin"])) ? $context["leftJoin"] : false);
		$where = ((isset($context["where"])) ? $context["where"] : false);
		$session = false;
		$change_id = false;
		if (isset($where["session"])) {
			$session = $where["session"];
			unset($where["session"]);
		}
		if (isset($where["change_id"])) {
			$change_id = $where["change_id"];
			unset($where["change_id"]);
		}
		if (isset($where["silent"])) {
			unset($where["silent"]);
		}
		$orderBy = ((isset($context["orderBy"])) ? $context["orderBy"] : current($select));
		$groupBy = (isset($context["groupBy"])) ? $context["groupBy"] : false;
		$limit = ((isset($context["limit"])) ? $context["limit"] : false);
		$offset = ((isset($context["offset"])) ? $context["offset"] : false);
		$random = ((isset($context["random"])) ? $context["random"] : false);
		$pk = ((isset($context["primary_key"])) ? $context["primary_key"] : false);

		$params = array();

		if (\runner::stack("model_create") && isset($model->route, \runner::stack("model_create")["route"])
			&& $model->route == \runner::stack("model_create")["route"]) {
			$load = array();
		} else {
			if (isset($where['sections'])) {
				unset($where['sections']);
			}

			if (isset($context["SQL"], $context["SQLhash"])
				&& \Routerunner\Crypt::checker($context["SQL"], $context["SQLhash"], "SQLchecked")
			) {
				$SQL = $context["SQL"];
				$params = $where;
			} else {
				$SQL = self::SQL_creator($select, $from, $pk, $leftJoin,
					$where, $params, $orderBy, $groupBy, $limit, $offset);
			}

			if (\runner::now("debug::model->load") === true) {
				\runner::now("debug::model->load", false);
				echo "debug::model->load" . PHP_EOL . $SQL . PHP_EOL . print_r($params, true);
			}
			$load = \Routerunner\Db::query($SQL, $params);
		}

		if ((!is_array($load) || !count($load)) && (isset($context['blank']) && $context['blank'] === true)) {
			foreach ($select as $field => & $value) {
				$value = '';
			}
			$load = array($select);
		}

		if (isset($model->override) && is_array($model->override) && count($model->override)) {
			if ($load && isset($load[0])) {
				$load[0] = array_merge($load[0], $model->override);
			} elseif ($load) {
				$load = array_merge($load, $model->override);
			} else {
				$load = array($model->override);
			}
		}

		if (is_array($load) && count($load) > 0) {
			$models = self::set_models($load, $model, $pk, $from, $random, $session);
			$model = $models;

			if (isset($context["force_list"]) && $context["force_list"] === true && !is_array($model)) {
				$model = array($model);
			} elseif (isset($context["force_view"]) && $context["force_view"] === true && is_array($model)) {
				$model = array_shift($model);
			}

			if (isset($context['pager']) && is_array($context['pager'])) {
				foreach ($context['pager'] as $pager_section => $pager_params) {
					if (is_array($pager_params)) {
						$pager_SQL_params = array();
						//$pager_params['select'] = (isset($pager_params['select'])) ? $pager_params['select'] : array('c' => 'COUNT(*)');
						$pager_params['primary_key'] = (isset($pager_params['primary_key'])) ? $pager_params['primary_key'] : $pk;
						$pager_params['select'] = (isset($pager_params['select'])) ? $pager_params['select'] : array($pager_params['primary_key'] => 'id');
						$pager_params['from'] = (isset($pager_params['from'])) ? $pager_params['from'] : $from;
						$pager_params['leftJoin'] = (isset($pager_params['leftJoin'])) ? $pager_params['leftJoin'] : $leftJoin;
						$pager_params['where'] = (isset($pager_params['where'])) ? $pager_params['where'] : $where;
						$pager_params['orderBy'] = (isset($pager_params['orderBy'])) ? $pager_params['orderBy'] : $orderBy;
						$pager_params['groupBy'] = (isset($pager_params['groupBy'])) ? $pager_params['groupBy'] : $groupBy;
						$pager_params['limit'] = (isset($pager_params['limit'])) ? $pager_params['limit'] : false;
						$pager_params['offset'] = (isset($pager_params['offset'])) ? $pager_params['offset'] : $offset;

						$pager_SQL = self::SQL_creator($pager_params['select'], $pager_params['from'],
							$pager_params['primary_key'], $pager_params['leftJoin'], $pager_params['where'],
							$pager_SQL_params, $pager_params['orderBy'], $pager_params['groupBy'],
							$pager_params['limit'], $pager_params['offset']);
						if ($result = \Routerunner\Db::query($pager_SQL, $pager_SQL_params)) {
							$pager[$pager_section] = count($result);
						} else {
							$pager[$pager_section] = 0;
						}
					} else {
						$pager[$pager_section] = $pager_params;
					}
				}
			}

			if (\runner::now("debug::model->return") === true) {
				\runner::now("debug::model->return", false);
				var_dump("debug::model->return", $model);
			}
			return $model;
		} elseif (\runner::config('mode') == 'backend' &&
			($model_create = \runner::stack("model_create")) && $model && isset($model_create["class"]) &&
			substr(get_class($model), strrpos(get_class($model), "\\") + 1) == $model_create["class"]) {
			$return = true;
			if (is_array($model_create)) {
				$created_model = $model;
				if (is_array($model)) {
					$created_model = $model[0];
				}
				foreach ($model_create as $var_name => $var_value) {
					if (!isset($created_model->$var_name) || $created_model->$var_name != $var_value) {
						$return = false;
					}
				}
			}
			if (!$return) {
				$model = null;
			}
			return $model;
		} else {
			if (\runner::now("debug::model->return") === true) {
				\runner::now("debug::model->return", false);
				var_dump("debug::model->return", null);
			}

			return null;

		}
	}

	public static function set_models($load, & $model, $pk="id", $from=false, $random=false, $session=false) {
		$return_models = array();
		$return_model = false;
		if (!$from) {
			$from = $model->class;
		}
		if (is_array($load) && count($load) > 0) {
			if ($pk) {
				$models = array();
				foreach ($load as $row) {
					$pkid = (isset($row[$pk]) ? $row[$pk] : hexdec(uniqid()));

					if (isset($models[$pkid])) {
						foreach ($row as $field => $value) {
							if (!is_null($value) && isset($models[$pkid][$field])) {
								if (is_array($models[$pkid][$field]) && !in_array($value, $models[$pkid][$field])) {
									$models[$pkid][$field][] = $value;
								} elseif (!is_array($models[$pkid][$field]) && $models[$pkid][$field] !== $value) {
									$models[$pkid][$field] = array($models[$pkid][$field], $value);
								}
							} elseif (!is_null($value)) {
								$models[$pkid][$field] = $value;
							}
						}
					} else {
						if (!isset($row[$pk])) {
							$row[$pk] = $pkid;
						}
						$models[$pkid] = $row;
					}
				}
			} else {
				$models = $load;
			}

			if (count($models) && $random) {
				shuffle($models);
				$random = (count($models) < $random) ? count($models) : $random;
				array_splice($models, $random);
			}


			if ($model->readable()) { // is the 'blank model' readable??
				while (count($models)) {
					$found = false;
					$model_data = array_shift($models);

					$tmp_model = clone $model;

					unset($model_data["route"], $model_data["class"], $model_data["reference"],
						$model_data["table_from"], $model_data["table_id"]);
					$tmp_model->set($model_data);

					if (!isset($context['skip_referencing']) || !$context['skip_referencing']) {
						$tmp_model->referencing($from,
							(($pk && isset($model_data[$pk])) ? $model_data[$pk] : current($model_data)));
					}

					if ($tmp_model->visible() || \runner::config('mode') == 'backend') {
						$model = $tmp_model;
						$found = true;
						if (!$return_model) {
							$return_model = $tmp_model;
						}
					}

					if ($found && $session) {
						$change = $model->draft($session);
						if ($change && isset($change[$model->reference])) {
							$model->set($change[$model->reference]);
						}
					}
					if ($found) {
						$return_models[$model->$pk] = $model;
					}
				}
			}
		}
		$model = $return_model;
		return $return_models;
	}

	private static function SQL_creator($select, $from, $primary_key, $leftJoin=array(), $where=array(),
										& $params=array(), $orderBy=false, $groupBy=false, $limit=false, $offset=false) {
		$ordering = $orderBy;
		$parents = \runner::stack("parents");
		if (!$parents) {
			$parents = array();
		}
		$prevs = \runner::stack("prevs");
		if (!$prevs) {
			$prevs = array();
		}
		$strict = false;
		if ($orderBy === \Routerunner\Routerunner::BY_TREE || $orderBy === \Routerunner\Routerunner::BY_TREE_DESC ||
			 $orderBy === \Routerunner\Routerunner::BY_INDEX || $orderBy === \Routerunner\Routerunner::BY_INDEX_DESC ||
			isset($where["direct"]) && $where["direct"]) {
			$tree = array();
			$params = array();

			if (isset($where["direct"]) && is_numeric($where["direct"])) {
				$SQL = 'SELECT models.reference, NULL AS parent_ref, NULL AS prev, table_from, table_id ' . PHP_EOL;
				$SQL .= 'FROM {PREFIX}models AS models ' . PHP_EOL;
				$SQL .= 'WHERE models.reference = :reference';
				$params = array(":reference" => $where["direct"]);
				$strict = true;
			} elseif (isset($where["direct"]) && is_array($where["direct"])) {
				$SQL = 'SELECT models.reference, NULL AS parent_ref, NULL AS prev, table_from, table_id ' . PHP_EOL;
				$SQL .= 'FROM {PREFIX}models AS models ' . PHP_EOL;
				$SQL .= 'WHERE models.model_class = :class AND models.table_id = :id';
				if (is_numeric(current($where["direct"]))) {
					$params = array(":class" => key($where["direct"]), ":id" => current($where["direct"]));
				} else {
					$params = array(":class" => current($where["direct"]), ":id" => key($where["direct"]));
				}
				$strict = true;
			} else {
				$where_reference = array();
				// get self reference
				if ($self_reference = self::resolve_model_reference('self', $where, true)) {
					$where_reference['model_traverse.reference IN (' . implode(',', $self_reference) . ')'] = null;
					$strict = true;
				}
				// get parent reference
				if ($parent_reference = self::resolve_model_reference('parent', $where, true)) {
					$where_reference['model_traverse.parent_ref IN (' . implode(',', $parent_reference) . ')'] = null;
					$strict = true;
				}
				// get lang condition
				if ($lang = self::resolve_model_reference('lang', $where, true)) {
					$where_reference['model_traverse.lang IN (' . implode(',', $lang) . ')'] = null;
					$strict = true;
				}
				if (!$where_reference && $where) {
					$SQL_reference = "SELECT models.reference FROM `" . $from .
						"` AS model_table " . PHP_EOL .
						"LEFT JOIN `" . $from . "` ON `" . $from . "`.`" . $primary_key .
							"` = model_table.`" . $primary_key . "`" . PHP_EOL .
						"LEFT JOIN `{PREFIX}models` AS models ON models.table_from = '" . $from .
						"' AND models.table_id = model_table.`" . $primary_key . "`" . PHP_EOL .
						" WHERE ";
					$conds_reference =  array();
					$params_reference =  array();
					foreach ($where as $cond_reference => $param_reference) {
						$conds_reference[] = preg_replace("~(:[a-z0-9\-_\.]+)~i", "?", $cond_reference);
						if (!is_null($param_reference)) {
							$params_reference[] = $param_reference;
						}
					}
					$SQL_reference .= implode(" AND ", $conds_reference);
					if ($result_reference = \db::query($SQL_reference, $params_reference)) {
						$row_reference = array_shift($result_reference);
						$reference_for_parent = $row_reference["reference"];

						if (($parent_obj = \Routerunner\Bootstrap::parent($reference_for_parent)) &&
							count($parent_obj)) {
							$parent_obj = array_pop($parent_obj);
							$parent_reference = $parent_obj["reference"];
							$where_reference['model_traverse.parent_ref IN (' . $parent_reference . ')'] = null;
						}
						$strict = true;
					}
				}

				if ($orderBy === \Routerunner\Routerunner::BY_TREE || $orderBy === \Routerunner\Routerunner::BY_TREE_DESC) {
					// get previous sibling
					$prev_reference = self::resolve_model_reference('prev', $where, true);
					if ($prev_reference !== false) {
						$where_reference['model_traverse.prev_ref IN (' . implode(',', $prev_reference) . ')'] = null;
						$strict = true;
					}
					// get next sibling
					// todo: check --- is it ok?
					$next_reference = self::resolve_model_reference('next', $where, true);
					if ($next_reference !== false) {
						$where_reference['model_traverse.prev_ref IN (SELECT reference FROM {PREFIX}model_trees WHERE prev_ref IN (' . implode(',', $next_reference) . '))'] = null;
						$strict = true;
					}
				}

				if ($orderBy === \Routerunner\Routerunner::BY_INDEX) {
					// get order_no sibling
					$order_no_reference = self::resolve_model_reference('order_no', $where, true);
					if ($order_no_reference !== false) {
						$where_reference['model_traverse.order_no IN (' . implode(',', $order_no_reference) . ')'] = null;
						$strict = true;
					}
				}

				if ($where_reference) {
					$SQL = 'SELECT models.reference, model_traverse.parent_ref' . PHP_EOL;
					if ($orderBy === \Routerunner\Routerunner::BY_TREE || $orderBy === \Routerunner\Routerunner::BY_TREE_DESC) {
						$SQL .= ', prev_ref AS prev' . PHP_EOL;
					} elseif ($orderBy === \Routerunner\Routerunner::BY_INDEX
						|| $orderBy === \Routerunner\Routerunner::BY_INDEX_DESC
					) {
						$SQL .= ', order_no AS prev' . PHP_EOL;
					}
					$SQL .= 'FROM {PREFIX}models AS models ' . PHP_EOL;
					if ($orderBy === \Routerunner\Routerunner::BY_TREE || $orderBy === \Routerunner\Routerunner::BY_TREE_DESC) {
						$SQL .= 'LEFT JOIN {PREFIX}model_trees AS model_traverse ON model_traverse.reference = models.reference ' . PHP_EOL;
					} else {
						$SQL .= 'LEFT JOIN {PREFIX}model_orders AS model_traverse ON model_traverse.reference = models.reference ' . PHP_EOL;
					}
					$SQL .= 'WHERE ' . implode(' AND ', array_keys($where_reference)) . PHP_EOL;
					if ($orderBy === \Routerunner\Routerunner::BY_INDEX) {
						$SQL .= 'ORDER BY model_traverse.parent_ref, model_traverse.order_no, models.reference';
					}
				}
			}

			if ($strict && ($result = \db::query($SQL, $params))) {
				// modify SQL params
				if (!$leftJoin) {
					$leftJoin = array();
				}
				array_unshift($leftJoin, '`' . $from . '` ON `' . $from . '`.`' . $primary_key . '` = models.table_id AND models.table_from = \'' . $from . '\'');
				$model_class = trim($from, '`');
				$primary_key = trim($primary_key, '`');
				$from = '{PREFIX}models AS models';

				if ($orderBy === \Routerunner\Routerunner::BY_INDEX
					|| $orderBy === \Routerunner\Routerunner::BY_INDEX_DESC) {
					foreach ($result as $reference_row) {
						$tree[] = $reference_row['reference'];
						$parents[$reference_row["reference"]] = $reference_row["parent_ref"];
						$prevs[$reference_row["reference"]] = $reference_row["prev"];
					}
					$orderBy = '`models`.`order_no`';
				} elseif ($orderBy === \Routerunner\Routerunner::BY_TREE
					|| $orderBy === \Routerunner\Routerunner::BY_TREE_DESC) {
					$orderBy = 'CASE `models`.`reference`';
					$reorder_tree = array();
					foreach ($result as $reference_row) {
						$reorder_tree[$reference_row['prev']] = $reference_row;
						$parents[$reference_row["reference"]] = $reference_row["parent_ref"];
						$prevs[$reference_row["reference"]] = $reference_row["prev"];
					}
					if (count($reorder_tree) === 1) {
						$prev = key($reorder_tree);
						$index = 0;
					} else {
						$prev = 0;
						$index = 0;
					}
					while (isset($reorder_tree[$prev])) {
						$current = $reorder_tree[$prev]['reference'];
						$tree[] = $current;
						$prev = $current;
						$orderBy .= ' WHEN ' . $current . ' THEN ' . $index;
						$index++;
					}
					$orderBy .= ' END';
				} elseif (isset($where["direct"])) {
					foreach ($result as $reference_row) {
						$tree[] = $reference_row['reference'];
					}
				}
				if ($ordering === \Routerunner\Routerunner::BY_INDEX_DESC
					|| $ordering === \Routerunner\Routerunner::BY_TREE_DESC) {
					$orderBy .= ' DESC';
				}
				if (!$where) {
					$where = array();
				}
				$where['models.reference IN (' . implode(',', $tree) . ')'] = null;
				$where['`' . $model_class . '`.`' . $primary_key . '` IS NOT NULL'] = null;
			} elseif ($strict) {
				$where['1 = 0'] = null;
			}

			if ($config_reference = \runner::config('reference')) {
				$where['models.reference IN (' . $config_reference . ')'] = null;
			}
		} else {
			// join models table and filter to reference
		}
		\runner::stack("parents", $parents);
		\runner::stack("prevs", $prevs);

		$SQL = '';
		unset($where["direct"]);
		$visible_references = array();
		$params = array();

		if (\runner::get("mode") != "backend") {
			$SQL = "SELECT models.reference FROM " . $from . PHP_EOL;
			if ($leftJoin) {
				foreach ($leftJoin as $join) {
					$SQL .= 'LEFT JOIN ' . $join . PHP_EOL;
				}
			}
			$SQL .= "LEFT JOIN {PREFIX}models AS models ON models.table_from = '" . $from . "' AND models.table_id = " .
				$from . "." . $primary_key . PHP_EOL;
			$_where = array();
			if (isset($where) && is_array($where)) {
				$_where = $where;
			}
			$_where["models.reference NOT IN (SELECT model FROM {PREFIX}model_states AS states WHERE states.active = 0 OR :time NOT BETWEEN COALESCE(begin, :time) AND COALESCE(end, :time))"] = time();
			if (is_array($_where) && count($_where)) {
				$SQL .= 'WHERE ';
				$i = 0;
				foreach ($_where as $filter => $param) {
					if (!is_numeric($filter))
						$SQL .= $filter;
					if (preg_match('/(:[a-z0-9]+)/i', $filter, $match)) {
						$params[$match[0]] = $param;
					} elseif (strpos($filter, '?') !== false) {
						$params[] = $param;
					}
					$i++;
					if (!is_numeric($filter) && $i < count($_where))
						$SQL .= ' AND ';
					$SQL .= PHP_EOL;
				}
			} elseif ($_where) {
				$SQL .= 'WHERE ' . $_where . PHP_EOL;
			}
			if ($state_results = \db::query($SQL, $params)) {
				foreach ($state_results as $state_result) {
					$visible_references[] = $state_result["reference"];
				}
			}

			$SQL = '';
			$params = array();
		}

		if (($orderBy !== \Routerunner\Routerunner::BY_INDEX && $orderBy !== \Routerunner\Routerunner::BY_INDEX_DESC
				&& $orderBy !== \Routerunner\Routerunner::BY_TREE
				&& $orderBy !== \Routerunner\Routerunner::BY_TREE_DESC) || $where) {

			$SQL = 'SELECT ';
			array_walk($select, function (&$value, $key) {
				if (is_null($value))
					$value = 'NULL AS `' . trim($key, '`') . '`';
				/*/elseif (substr($value, 0, 1) != '`' && substr($value, -1) != '`' && (strpos($value, '.') === false && str_word_count($value) == 1)
						&& strpos($value, '(') === false && strpos($value, ')') === false)
					$value = '`' . $value . '` AS `'.trim($key, '`').'`';*/
				else
					$value = $value . ' AS `' . trim($key, '`') . '`';
			});
			$SQL .= implode(', ', $select);
			$SQL .= PHP_EOL;

			$SQL .= 'FROM ' . $from . PHP_EOL;

			if ($leftJoin) {
				foreach ($leftJoin as $join) {
					$SQL .= 'LEFT JOIN ' . $join . PHP_EOL;
				}
			}
			if ($visible_references) {
				$SQL .= "LEFT JOIN {PREFIX}models AS models ON models.table_from = '" . $from .
					"' AND models.table_id = " . $from . "." . $primary_key . PHP_EOL;
				if (!isset($where) || !is_array($where)) {
					$where = array();
				}
				$where["models.reference IN (" . implode(",", $visible_references) . ")"] = null;
			}

			if (is_array($where) && count($where)) {
				$SQL .= 'WHERE ';
				$i = 0;
				foreach ($where as $filter => $param) {
					if (!is_numeric($filter))
						$SQL .= $filter;
					if (preg_match('/(:[a-z0-9]+)/i', $filter, $match)) {
						$params[$match[0]] = $param;
					} elseif (strpos($filter, '?') !== false) {
						$params[] = $param;
					}
					$i++;
					if (!is_numeric($filter) && $i < count($where))
						$SQL .= ' AND ';
					$SQL .= PHP_EOL;
				}
			} elseif ($where) {
				$SQL .= 'WHERE ' . $where . PHP_EOL;
			}

			if ($groupBy)
				$SQL .= 'GROUP BY ' . $groupBy . PHP_EOL;

			if ($orderBy != "BY_TREE" && $orderBy != "BY_TREE_DESC"
				&& $orderBy != "BY_INDEX" && $orderBy != "BY_INDEX_DESC") {
				if ($orderBy)
					$SQL .= 'ORDER BY ' . $orderBy . PHP_EOL;
				elseif ($orderBy !== false)
					$SQL .= 'ORDER BY ' . $primary_key . PHP_EOL;
			}

			if ($limit !== false) {
				$SQL .= 'LIMIT ' . $limit . PHP_EOL;
				if ($offset !== false) {
					$SQL .= 'OFFSET ' . $offset . PHP_EOL;
				}
			} elseif ($limit === false && $offset !== false) {
				$SQL .= 'LIMIT 18446744073709551615' . PHP_EOL;
				$SQL .= 'OFFSET ' . $offset . PHP_EOL;
			}
		}
		return $SQL;
	}

	private static function resolve_model_reference($sibling, & $where=array(), $strict=false)
	{
		$reference = ($strict === true) ? false : array(0);

		if (isset($where[$sibling])) {
			$params = $where[$sibling];
			unset($where[$sibling]);
			if (!is_array($params) && is_numeric($params)) {
				$reference = array($params);
			} elseif (is_array($params)) {
				if (isset($params['reference'])) {
					$reference = array($params['reference']);
				} elseif (!(current($params))) {
					// do nothing
				} else {
					$SQL = 'SELECT reference FROM {PREFIX}models WHERE model_class = :class AND table_id = :id';
					$params_SQL = array(
						':class' => key($params),
						':id' => current($params),
					);
					if ($result = \db::query($SQL, $params_SQL)) {
						$reference = array();
						foreach ($result as $row) {
							$reference[] = $row['reference'];
						}
					}
				}
			}
		}

		return $reference;
	}
}