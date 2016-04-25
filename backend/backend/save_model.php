<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.09.08.
 * Time: 15:33
 */
require '../../Routerunner/Routerunner.php';
use \Routerunner\Routerunner as runner;

$reference = (isset($_POST['reference']) ? $_POST['reference'] : false);
$route = (isset($_POST['route']) ? $_POST['route'] : false);
$change = (isset($_POST['model']) ? $_POST['model'] : false);

$model_class = (isset($_POST['model_class']) ? $_POST['model_class'] : false);
$table_from = (isset($_POST['table_from']) ? $_POST['table_from'] : false);
$parent = (isset($_POST['parent']) ? $_POST['parent'] : false);
$prev = (isset($_POST['prev']) ? $_POST['prev'] : false);
$order_no = (isset($_POST['order_no']) ? $_POST['order_no'] : false);
$lang = (isset($_POST['lang']) ? $_POST['lang'] : 0);

new runner(array(
	'mode' => 'cms',
), function() use ($reference, $route, $change, $model_class, $table_from, $parent, $prev, $order_no, $lang) {

	//if (is_array($reference) && isset($reference['model_class'])) {
	if (!$reference && $model_class) {
		// insert new table row
		$table = ($table_from ? $table_from : $model_class);

		$SQL_table = <<<SQL
SELECT column_name, is_nullable, data_type, character_maximum_length, column_default
FROM information_schema.columns
WHERE table_schema = DATABASE() AND table_name = :table
AND CASE WHEN column_key LIKE 'PRI%' THEN 1 ELSE 0 END = 0

SQL;
		$fields = array();
		if ($result = \db::query($SQL_table, array(':table' => $table))) {
			foreach ($result as $row) {
				if (strtolower($row['is_nullable']) == 'no') {
					if (!is_null($row['column_default'])) {
						$fields[$row['column_name']] = $row['column_default'];
					} else {
						if (strpos($row['data_type'], 'char') !== false
							|| strpos($row['data_type'], 'text') !== false
							|| strpos($row['data_type'], 'blob') !== false
							|| strpos($row['data_type'], 'binary') !== false
						) {
							$fields[$row['column_name']] = '';
						} elseif (strpos($row['data_type'], 'int') !== false
							|| strpos($row['data_type'], 'decimal') !== false
							|| strpos($row['data_type'], 'double') !== false
							|| strpos($row['data_type'], 'float') !== false
						) {
							$fields[$row['column_name']] = 0;
						} else {
							$fields[$row['column_name']] = false;
						}
					}
				}
			}

			$keys = array_keys($fields);
			array_walk($keys, function(& $value, $index){
				$value = '`' . $value . '`';
			});
			$params = array();
			foreach ($fields as $key => $value) {
				$params[':' . $key] = $value;
			}

			$SQL_table = 'INSERT INTO `' . $table . '` (' . implode(', ', $keys) . ') VALUES (' .
				implode(', ', array_keys($params)) . ')';
			if ($primary_id = \db::insert($SQL_table, $params)) {
				if (!$table_from) {
					$table_from = $table;
				}
				$table_id = $primary_id;

				// insert new model
				$SQL_SP = 'CALL `{PREFIX}model_reference`(?, ?, ?)';
				if ($reference_id = \db::query($SQL_SP,
					array($model_class, $table_from, $table_id))) {
					$reference = $reference_id[0]['reference'];
				}
			}
		}
	}


	$router = false;
	$fields = array();
	if ($model = model::load(array('self' => array('reference' => $reference)), $route, $router)) {
		$fields = $router->runner->backend_context['model']['fields'];
	}

	if (!$model && $parent !== false) {
		if ($prev !== false &&
			($router->runner->model_context['orderBy'] === \Routerunner\Routerunner::BY_TREE
				|| $router->runner->model_context['orderBy'] === \Routerunner\Routerunner::BY_TREE_DESC)) {
			$SQL_SP = 'CALL `{PREFIX}tree_insert`(?, ?, ?, ?)';
			\db::query($SQL_SP, array($reference, $parent, $prev, $lang));
		} elseif ($order_no !== false &&
			($router->runner->model_context['orderBy'] === \Routerunner\Routerunner::BY_INDEX
				|| $router->runner->model_context['orderBy'] === \Routerunner\Routerunner::BY_INDEX_DESC)) {
			$SQL_SP = 'CALL `{PREFIX}order_insert`(?, ?, ?, ?)';
			\db::query($SQL_SP, array($reference, $parent, $order_no, $lang));
		}
		$router = false;
		\Routerunner\Routerunner::$static->config('mode', 'blank');
		if ($model = model::load(array('self' => array('reference' => $reference)), $route, $router, true)) {
			$fields = $router->runner->backend_context['model']['fields'];
		}
		\Routerunner\Routerunner::$static->config('mode', 'cms');
	}


	$SQL = <<<SQL
SELECT models.model_class, models.table_from, models.table_id FROM `{PREFIX}models` AS models
WHERE models.reference = :reference

SQL;
	$update = array();

	if ($result = \db::query($SQL, array(':reference' => $reference))) {
		$model_data = array_shift($result);
		$pk = false;

		if (is_array($change)) {
			$SQL = <<<SQL
SELECT k.column_name
FROM information_schema.table_constraints t
LEFT JOIN information_schema.key_column_usage k
USING (constraint_name,table_schema,table_name)
WHERE t.constraint_type = 'PRIMARY KEY'
    AND t.table_schema = DATABASE()
    AND t.table_name = :table

SQL;
			if ($cols = \db::query($SQL, array(':table' => $model_data['table_from']))) {
				$pk = $cols[0]['column_name'];
			}

			$SQL = <<<SQL
SELECT column_name, is_nullable, data_type, character_maximum_length, column_default,
CASE WHEN column_key LIKE 'PRI%' THEN 1 ELSE 0 END AS primary_key
FROM information_schema.columns
WHERE table_schema = DATABASE() AND table_name = :table

SQL;
			if ($pk && ($cols = \db::query($SQL, array(':table' => $model_data['table_from'])))) {
				$columns = array();
				foreach ($cols as $column) {
					$columns[$column['column_name']] = $column;
				}

				$i = 1;
				$SQL = 'UPDATE `' . $model_data['table_from'] . '` SET ' . PHP_EOL;
				foreach ($change as $field => $value) {
					if (isset($columns[$field])) {
						$column = $columns[$field];

						// check field
						$msg = array();
						$check = true;
						if (isset($fields[$field])) {
							$field_params = $fields[$field];

							if (isset($field_params['mandatory']['value']) && $field_params['mandatory']['value']
								&& (!$value || is_null($value))) {
								$check = false;
								$msg[] = (isset($field_params['mandatory']['msg'])
									? $field_params['mandatory']['msg'] : 'Missing field: ' . $field);
							}

							if (isset($field_params['regexp'])) {
								$regexp_pattern = (isset($field_params['regexp']['value'])
									? '/' . $field_params['regexp']['value'] . '/' .
									(isset($field_params['regexp']['options'])
										? str_replace('g', '', $field_params['regexp']['options']) : '') : '');

								if ($regexp_pattern && !preg_match($regexp_pattern, $value)) {
									$check = false;
									$msg[] = (isset($field_params['regexp']['msg'])
										? $field_params['regexp']['msg'] : 'Field in unacceptable format: ' . $field);
								}
							}
						}

						if ($check && (!isset($model->$field) || $value !== $model->$field)) {
							$SQL .= '`' . $field . '` = :' . $field . (($i < count($change)) ? ', ' : '') . PHP_EOL;
							if (isset($fields[$field]['input'])) {
								if (is_array($fields[$field]['input'])
									&& function_exists($fields[$field]['input'][0])) {
									$fn = $fields[$field]['input'][0];
									array_shift($fields[$field]['input']);
									if ($fn) {
										$update[$field] = $fn($value, $fields[$field]['input']);
									}
								} elseif (is_string($fields[$field]['input'])
									&& function_exists($fields[$field]['input'])) {
									$update[$field] = $fields[$field]['input']($value);
								} else {
									$update[$field] = $value;
								}
							} else {
								$update[$field] = $value;
							}
						}
					}
					$i++;
				}
				if ($update) {
					$SQL .= 'WHERE `' . $pk . '` = :table_id';
					$save = array();
					foreach ($update as $key => $value) {
						$save[':' . $key] = $value;
					}
					$save[':table_id'] = $model_data['table_id'];
					\db::query($SQL, $save);

					$SQL = <<<SQL
INSERT INTO `{PREFIX}history` (`reference`, `date`, `user`, `model`) VALUES (:reference, :date, :user, :model)

SQL;
					$params = array(
						':reference' => $reference,
						':date' => time(),
						':user' => 1,
						':model' => json_encode($update),
					);
					\db::query($SQL, $params);
				}

			}
		}
	}
});