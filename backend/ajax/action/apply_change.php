<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.10.20.
 * Time: 10:33
 */

header('Content-Type: application/json');

$require = '../../../';
try {
    $require = \runner::config('SITEROOT') . \runner::config('BACKEND_ROOT');
} catch (Exception $e) {

}
if (!class_exists('\Routerunner\Routerunner', false)) {
    require $require . 'Routerunner/Routerunner.php';
}
use \Routerunner\Routerunner as runner;

$post = array_merge($_GET, $_POST);

new runner(array(
	'mode' => 'backend',
	'params' => $post,
	'method' => 'post',
	'resource' => '/',
	'bootstrap' => false,
), function() use ($post) {
	$settings = \Routerunner\Routerunner::$static->settings;
	\Routerunner\Bootstrap::initialize($settings, true);

	$response = array(
		"success" => false,
		"apply" => false,
		"change_id" => false,
		"error" => array(),
	);

	$SQL = "CALL {PREFIX}change_get(:change_id, :session, :draft, :applied)";
	$params = array(
		":change_id" => (is_numeric($post["change_id"]) ? $post["change_id"] : 0),
		":session" => (\runner::stack("session_id") ? \runner::stack("session_id") : 0),
		":draft" => 1,
		":applied" => 0,
	);
	if ($change_get = \db::query($SQL, $params)) {
		foreach ($change_get as $change) {
			$response["change_id"] = $change["change_id"];

            $SQL_apply = "CALL `{PREFIX}change_apply`(:change_id, :session)";
            $params_apply = array(
                ":change_id" => $change["change_id"],
                ":session" => (\runner::stack("session_id") ? \runner::stack("session_id") : 0),
            );

			$model_class = false;
			if (is_numeric($change["reference"])) {
				$SQL = 'SELECT model_class FROM {PREFIX}models WHERE reference = ?';
				if ($result = \db::query($SQL, array($change["reference"]))) {
					$model_class = $result[0]["model_class"];
				}
			}

			if (($resource = json_decode($change["resource"], true)) && isset($resource["route"])) {
				$model_route = $resource["route"];
			} else {
				$model_class = "get_the_proper_model_class";
				$model_route = "/model";
			}
			if ($model_class && substr($model_route, -1*(strlen($model_class) + 1)) != '/' . $model_class) {
				$model_route .= '/' . $model_class;
			}
			$context = array(
				"direct" => $change["reference"],
				"session" => \runner::stack("session_id"),
				"silent" => true,
			);
			$router = false;
			$model = false;

			\runner::stack("model_create", array("class" => $model_class));
			\runner::redirect_route($model_route, \runner::config("scaffold"), true, $context, $router, $model);
			\runner::stack("model_create", false);

			if ($model && is_array($model)) {
				$model = array_shift($model);
			}

			if ((is_object($model) && get_parent_class($model) == "Routerunner\\BaseModel")
				&& ($changes = json_decode($change["changes"], true))) {
				switch ($change["state"]) {
					case "property":
						foreach ($changes as $field => $value) {
							if (property_exists($model, $field)
								&& isset($model->table_from) && isset($model->table_id)) {
								$model->$field = $value;
								$pk = false;

								if (isset($router->runner->model_context["primary_key"])) {
									$pk = $router->runner->model_context["primary_key"];
								} else {
									$SQL = <<<SQL
SELECT k.column_name
FROM information_schema.table_constraints t
LEFT JOIN information_schema.key_column_usage k
USING (constraint_name,table_schema,table_name)
WHERE t.constraint_type = 'PRIMARY KEY'
AND t.table_schema = DATABASE()
AND t.table_name = :table

SQL;
									if ($result = \db::query($SQL, array(":table" => trim($model->table_from, ' `')))) {
										$pk = $result[0]["column_name"];
									}
								}
								if ($pk) {
									$SQL = <<<SQL
SELECT column_name, is_nullable, data_type, character_maximum_length, column_default,
CASE WHEN column_key LIKE 'PRI%' THEN 1 ELSE 0 END AS primary_key
FROM information_schema.columns
WHERE table_schema = DATABASE() AND table_name = :table AND column_name = :field

SQL;
									if ($field_data = \db::query($SQL,
										array(":table" => trim($model->table_from, ' `'), ":field" => $field))) {

										$is_wrong = false;
										if (isset($router->runner->backend_context["model"]["fields"][$field]) &&
											($field_context = $router->runner->backend_context["model"]["fields"][$field])) {
											if (isset($field_context["mandatory"])
												&& $field_context["mandatory"]["value"] === true && !$value) {
												$response["error"][] = $field_context["mandatory"]["msg"];
												$is_wrong = true;
											}
											if (isset($field_context["regexp"])) {
												$pattern = "/" . $field_context["regexp"]["value"] . "/" .
													$field_context["regexp"]["options"];
												if (!preg_match($pattern, $value)) {
													$response["error"][] = $field_context["regexp"]["msg"];
													$is_wrong = true;
												}
											}

											if ($is_wrong && isset($field_context["default"])) {
												$value = $field_context["default"]["value"];
												$response["error"][] = $field_context["default"]["msg"];
												$is_wrong = false;
											}
										}

										if (!$is_wrong) {
											if (isset($field_context["input"]) && $field_context["input"]) {
												if (is_array($field_context["input"])) {
													$fn = array_shift($field_context["input"]);
													$args = $field_context["input"];
													foreach ($args as & $arg_val) {
														if ($arg_val == "value") {
															$arg_val = $value;
														}
													}
												} else {
													$fn = $field_context["input"];
													$args = (is_array($value) ? $value :  array($value));
												}
												if (function_exists($fn)) {
													$value = call_user_func_array($fn, $args);
												}
											}

											if (is_array($value)) {
												if (isset($field_context["delimiter"])) {
													$value = implode($field_context["delimiter"], $value);
												} else {
													$value = json_encode($value, JSON_UNESCAPED_SLASHES);
												}
											}
											if (isset($field_context["type"]) && $field_context["type"] == "checkbox") {
												$value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
												$value = ($value ? "1" : "0");
											}

											$SQL_UPDATE = 'UPDATE `' . $model->table_from . '` SET `' . $field .
												'` = :value WHERE `' . $pk . '` = :id';
											$params_update = array(
												":value" => $value,
												":id" => $model->table_id,
											);
											\db::query($SQL_UPDATE, $params_update);
                                            $response["success"] = true;

											if ($result_apply = \db::query($SQL_apply, $params_apply)) {
												$response["apply"] = $result_apply[0];
											}

											if (isset($field_context["is_label"]) && $field_context["is_label"]) {

												$SQL_rewrite = 'SELECT `rewrite_id` FROM `{PREFIX}rewrites` ';
												$SQL_rewrite .= 'WHERE `reference` = :reference';
												$params_rewrite = array(
													":reference" => $model->reference,
												);
												if (!\db::query($SQL_rewrite, $params_rewrite)) {
													$SQL_rewrite = <<<SQL
INSERT INTO `{PREFIX}rewrites` (`url`, `resource_uri`, `reference`, `primary`)
VALUES (:url, :resource_uri, :reference, :primary)

SQL;
													$resource_uri = $model->class . '/' . $model->table_id;
													$ascii = \runner::get_rewrite_url($value,
														$model->reference, $resource_uri);
													$params_rewrite = array(
														':url' => $ascii,
														':resource_uri' => $resource_uri,
														':reference' => $model->reference,
														':primary' => 1,
													);
													\db::insert($SQL_rewrite, $params_rewrite);
												}

												// save title
												$SQL_meta = 'SELECT model_meta_id, title FROM `{PREFIX}model_metas` ';
												$SQL_meta .= 'WHERE `reference` = :reference';
												$params_meta = array(
													":reference" => $model->reference,
												);
												$result_meta = \db::query($SQL_meta, $params_meta);

												$title = '';
												$prefixes = \bootstrap::get("pageproperties_prefix");
												if ($prefixes && isset($prefixes["title"])) {
													$title .= $prefixes["title"];
												}
												$title .= $value;

												if (!$result_meta) {
													$SQL_meta = <<<SQL
INSERT INTO `{PREFIX}model_metas` (`reference`, `title`) VALUES (:reference, :title)

SQL;
													$params_meta = array(
														":reference" => $model->reference,
														":title" => strip_tags($title),
													);
													\db::insert($SQL_meta, $params_meta);
												} elseif (is_null($result_meta[0]["title"])
													|| (isset($field_context["force_title"])
														&& $field_context["force_title"])) {
													$SQL_meta = <<<SQL
UPDATE `{PREFIX}model_metas` SET `title` = :title WHERE `reference` = :reference

SQL;
													$params_meta = array(
														":reference" => $model->reference,
														":title" => $title,
													);
													\db::query($SQL_meta, $params_meta);
												}
											}

											// save keywords
											if (isset($field_context["is_keywords"]) && $field_context["is_keywords"]) {
												$SQL_meta = 'SELECT model_meta_id, keywords FROM `{PREFIX}model_metas` ';
												$SQL_meta .= 'WHERE `reference` = :reference';
												$params_meta = array(
													":reference" => $model->reference,
												);
												$result_meta = \db::query($SQL_meta, $params_meta);

												$keywords = '';
												$prefixes = \bootstrap::get("pageproperties_prefix");
												if ($prefixes && isset($prefixes["keywords"])) {
													$keywords .= trim($prefixes["keywords"], " ,") . ", ";
												}
												if (isset($field_context["delimiter"])) {
													$keywords_tmp = explode(",", $keywords);
													$keywords_tmp = array_merge($keywords_tmp,
														explode($field_context["delimiter"], $value));
													$keywords_arr = array();
													foreach ($keywords_tmp as $keyword) {
														if ($trimmed = trim($keyword)) {
															$keywords_arr[] = $trimmed;
														}
													}
													$keywords = implode(",", $keywords_arr);
												} else {
													$keywords .= $value;
												}

												if (!$result_meta) {
													$SQL_meta = <<<SQL
INSERT INTO `{PREFIX}model_metas` (`reference`, `keywords`) VALUES (:reference, :keywords)

SQL;
													$params_meta = array(
														":reference" => $model->reference,
														":keywords" => strip_tags($keywords),
													);
													\db::insert($SQL_meta, $params_meta);
												} elseif (is_null($result_meta[0]["keywords"])
													|| (isset($field_context["force_keywords"])
														&& $field_context["force_keywords"])) {
													$SQL_meta = <<<SQL
UPDATE `{PREFIX}model_metas` SET `keywords` = :keywords WHERE `reference` = :reference

SQL;
													$params_meta = array(
														":reference" => $model->reference,
														":keywords" => strip_tags($keywords),
													);
													\db::query($SQL_meta, $params_meta);
												}
											}

											// save description
											if (isset($field_context["is_description"])
												&& $field_context["is_description"]) {
												$SQL_meta = 'SELECT model_meta_id, description ';
												$SQL_meta .= 'FROM `{PREFIX}model_metas` ';
												$SQL_meta .= 'WHERE `reference` = :reference';
												$params_meta = array(
													":reference" => $model->reference,
												);
												$result_meta = \db::query($SQL_meta, $params_meta);

												$description = '';
												$prefixes = \bootstrap::get("pageproperties_prefix");
												if ($prefixes && isset($prefixes["description"])) {
													$description .= $prefixes["description"];
												}
												$description .= $value;

												if (!$result_meta) {
													$SQL_meta = <<<SQL
INSERT INTO `{PREFIX}model_metas` (`reference`, `description`) VALUES (:reference, :description)

SQL;
													$params_meta = array(
														":reference" => $model->reference,
														":description" => preg_replace("/\\t/im", "", preg_replace("/\\n\\n/im", "\n", strip_tags($description))),
													);
													\db::insert($SQL_meta, $params_meta);
												} elseif (is_null($result_meta[0]["description"])
													|| (isset($field_context["force_description"])
														&& $field_context["force_description"])) {
													$SQL_meta = <<<SQL
UPDATE `{PREFIX}model_metas` SET `description` = :description WHERE `reference` = :reference

SQL;
													$params_meta = array(
														":reference" => $model->reference,
														":description" => preg_replace("/\\t/im", "", preg_replace("/\\n\\n/im", "\n", strip_tags($description))),
													);
													\db::query($SQL_meta, $params_meta);
												}
											}
										}
									}
								}
							}
							//next($changes);
						}
						break;
					case "visibility":
						$SQL_CHECK = "SELECT model_state_id FROM `{PREFIX}model_states` WHERE `model` = ?";
						if (!\db::query($SQL_CHECK, array($model->reference))) {
							$SQL_STATE_INSERT = "INSERT INTO `{PREFIX}model_states` (model) VALUES (?)";
							\db::insert($SQL_STATE_INSERT, array($model->reference));
						}

						foreach ($changes as $field => $value) {
							$params_update = false;
							switch ($field) {
								case "section":
									switch ($value) {
										case "visibility-simple":
											$SQL_UPDATE = "UPDATE `{PREFIX}model_states` SET `begin` = NULL, " .
												"`end` = NULL, `params` = NULL WHERE `model` = :reference";
											$params_update = array(":reference" => $model->reference);
											break;
										case "visibility-date":
											$SQL_UPDATE = "UPDATE `{PREFIX}model_states` SET `active` = 1, " .
												"`params` = NULL WHERE `model` = :reference";
											$params_update = array(":reference" => $model->reference);
											break;
									}
									break;
								default:
									$SQL_UPDATE = "UPDATE `{PREFIX}model_states` SET `" . $field . "` = :value " .
										"WHERE `model` = :reference";
									if ($field == "begin" || $field == "end") {
										if (!$value) {
											$value = null;
										} elseif ($time = strtotime($value)) {
											$value = $time;
										} else {
											$value = null;
										}
									} elseif ($field == "active") {
										$value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
									}
									$params_update = array(":value" => $value, ":reference" => $model->reference);
									break;
							}
							if ($params_update) {
								\db::query($SQL_UPDATE, $params_update);

                                $response["success"] = true;

								if ($result_apply = \db::query($SQL_apply, $params_apply)) {
									$response["apply"] = $result_apply[0];
								}
							}
						}
						break;
					case "position":
						if (isset($changes["from"]) && isset($changes["to"])) {
							if (!is_array($changes["from"])) {
								$changes["from"] = filter_var($changes["from"], FILTER_VALIDATE_BOOLEAN);
							}
							if (!is_array($changes["to"])) {
								$changes["to"] = filter_var($changes["to"], FILTER_VALIDATE_BOOLEAN);
							}

							if (!$changes["from"] && is_array($changes["to"])
								&& isset($changes["to"]["parent"], $changes["to"]["prev"])) {
								// insert model
								$table_id = false;

								// check for the existing table row
								if (is_numeric($model->table_id) && $model->table_id > 0) {
									if (isset($router->runner->model_context["primary_key"])) {
										$pk = $router->runner->model_context["primary_key"];
									} else {
										$SQL = <<<SQL
SELECT k.column_name
FROM information_schema.table_constraints t
LEFT JOIN information_schema.key_column_usage k
USING (constraint_name,table_schema,table_name)
WHERE t.constraint_type = 'PRIMARY KEY'
AND t.table_schema = DATABASE()
AND t.table_name = :table

SQL;
										if ($result = \db::query($SQL, array(":table" => trim($model->table_from, ' `')))) {
											$pk = $result[0]["column_name"];
										}
									}
									if ($pk) {
										$SQL = "SELECT `" . $pk . "` AS table_id FROM `" . trim($model->table_from, ' `') .
											"` WHERE `" . $pk . "` = :table_id";
										if (!($result = \db::query($SQL, array(":table_id" => $model->table_id)))) {
											$model->table_id = false;
										}
									}
								} else {
									$model->table_id = false;
								}

								if (!$model->table_id) {
									// create table specific row to model

									$not_nullable_fields = array();

									$SQL = <<<SQL
SELECT column_name, is_nullable, data_type, character_maximum_length, column_default,
CASE WHEN column_key LIKE 'PRI%' THEN 1 ELSE 0 END AS primary_key
FROM information_schema.columns
WHERE table_schema = DATABASE() AND table_name = :table AND is_nullable = 'NO' AND EXTRA NOT LIKE '%auto_increment%'

SQL;
									if ($result = \db::query($SQL, array(":table" => trim($model->table_from, ' `')))) {
										foreach ($result as $table_field) {
											$field = $table_field["column_name"];
											$value = null;
											if (isset($router->runner->backend_context["model"]["fields"][$field])
												&& ($field_context =
													$router->runner->backend_context["model"]["fields"][$field])) {
												if (isset($field_context["default"])
													&& isset($field_context["default"]["value"])) {
													$value = $field_context["default"]["value"];
												} elseif (isset($field_context["default"])) {
													$value = $field_context["default"];
												}
											}

											if (is_null($value)) {
												if (!is_null($table_field["column_default"])) {
													$value = $table_field["column_default"];
												} else {
													if (strpos($table_field["data_type"], "int") !== false
														|| strpos($table_field["data_type"], "decimal") !== false
														|| strpos($table_field["data_type"], "double") !== false
														|| strpos($table_field["data_type"], "float") !== false) {
														$value = 0;
													} elseif (strpos($table_field["data_type"], "date") !== false
														|| strpos($table_field["data_type"], "time") !== false) {
														$value = 0;
													} elseif (strpos($table_field["data_type"], "char") !== false
														|| strpos($table_field["data_type"], "text") !== false) {
														$value = '';
													} elseif (strpos($table_field["data_type"], "blob") !== false) {
														$value = '';
													}
												}
											}

											if (is_null($value)) {
												throw new ErrorException("Null value to not nullable field!");
											} else {
												$not_nullable_fields[$field] = $value;
											}
										}
									}

									$fields = array();
									$params_insert = array();

									foreach ($not_nullable_fields as $field => $value) {
										$field_name = trim($field, " \t\n\r\0\x0B`'\"");
										$fields[] = "`" . $field_name . "`";
										$params_insert[":" . $field_name] = $value;
									}

									$SQL_INSERT = "INSERT INTO `" . trim($model->table_from, " `") . "` (" .
										implode(", ", $fields) . ") VALUES (" .
										implode(", ", array_keys($params_insert)) . ")";
									if ($table_id = \db::insert($SQL_INSERT, $params_insert)) {
										// update model reference row with table_id
										$model->table_id = $table_id;
										$SQL_UPDATE = <<<SQL
UPDATE `{PREFIX}models` SET `table_id` = :table_id WHERE `reference` = :reference
SQL;
										\db::query($SQL_UPDATE, array(
											":table_id" => $table_id,
											":reference" => $model->reference
										));
									}
								}

								// place new model into the tree
								$SQL_SP = 'CALL `{PREFIX}tree_insert`(?, ?, ?, ?)';
								$params_SP = array(
									$model->reference,
									$changes["to"]["parent"],
									$changes["to"]["prev"],
									0);
								if (\db::query($SQL_SP, $params_SP)) {
									$response["success"] = true;

									if ($result_apply = \db::query($SQL_apply, $params_apply)) {
										$response["apply"] = $result_apply[0];

										$model->permissioning($changes["to"]["parent"], $router->runner);
										if ($model->permission && !$model->activate_allowed()) {
											$SQL_STATE_INSERT = "INSERT INTO `{PREFIX}model_states` (`model`, `active`)
																	VALUES (?, ?)";
											\db::insert($SQL_STATE_INSERT, array($model->reference, 0));
										}
									}
								}

							} elseif ($changes["from"] && is_array($changes["from"])
								&& isset($changes["from"]["parent"], $changes["from"]["prev"]) && !$changes["to"]) {
								// remove model

								$SQL_SP = 'CALL `{PREFIX}tree_remove`(?)';

								$params_SP = array($model->reference);
								\db::query($SQL_SP, $params_SP);
								$response["success"] = true;

								$pk = $router->runner->model_context["primary_key"];
								$SQL_DELETE = "DELETE FROM `" . trim($model->table_from, " `") . "` WHERE `" .
									$pk . "` = :id";
								\db::query($SQL_DELETE, array(':id' => $model->table_id));

								if ($result_apply = \db::query($SQL_apply, $params_apply)) {
									$response["apply"] = $result_apply[0];
								}

							} elseif ($changes["from"] && is_array($changes["from"])
								&& isset($changes["from"]["parent"], $changes["from"]["prev"]) && $changes["to"]
								&& is_array($changes["to"])
								&& isset($changes["to"]["parent"], $changes["to"]["prev"])) {
								// move model

								$SQL_SP = 'CALL `{PREFIX}tree_moveto`(?, ?, ?, ?)';
								$params_SP = array(
									$model->reference,
									$changes["to"]["parent"],
									$changes["to"]["prev"],
									0);
								if (\db::query($SQL_SP, $params_SP)) {
									$response["success"] = true;

									if ($result_apply = \db::query($SQL_apply, $params_apply)) {
										$response["apply"] = $result_apply[0];
									}
								}

							}
						}
						break;
				}
			}
		}

	}

	echo json_encode($response);
});