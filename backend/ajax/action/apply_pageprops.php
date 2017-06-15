<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.10.20.
 * Time: 10:33
 */
require '../../../Routerunner/Routerunner.php';
use \Routerunner\Routerunner as runner;

$post = array_merge($_GET, $_POST);

new runner(array(
	'mode' => 'backend',
	'params' => $post,
	'method' => 'post',
	'resource' => '/',
	'bootstrap' => false,
), function() use ($post) {
	$response = array(
		"success" => false,
		"apply" => false,
		"change_id" => false,
		"error" => array(),
	);

	$SQL = "CALL {PREFIX}change_get(:change_id, :session, :draft, :applied)";
	$params = array(
		":change_id" => (is_numeric($post["change_id"]) ? $post["change_id"] : null),
		":session" => (\runner::stack("session_id") ? \runner::stack("session_id") : null),
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




			if ($change["state"] == "routerunner-page-properties" && ($changes = json_decode($change["changes"], true))
				&& ($resource = json_decode($change["resource"], true))) {
				foreach ($changes as $field => $value) {
					switch ($field) {
						case "url":
						case "urls":
							$condition = array();
							$params = array();
							if (isset($resource["reference"]) && $resource["reference"]) {
								$condition['reference'] = '`reference` = :reference';
								$params[":reference"] = $resource["reference"];
							}
							if (isset($resource["resource_uri"]) && $resource["resource_uri"]) {
								$condition['resource_uri'] = '`resource_uri` = :resource_uri';
								$params[":resource_uri"] = $resource["resource_uri"];
							}
							if (!empty($resource["params"])) {
								$resource["params"] = str_replace('async=true', '', $resource["params"]);
							}
							if (!empty($resource["params"])) {
								$condition['params'] = '`params` = :params';
								$params[":params"] = $resource["params"];
							}
							/*
							if (isset($resource["lang"]) && $resource["lang"]) {
								$condition['lang'] = '`lang` = :lang';
								$params[":lang"] = $resource["lang"];
							}
							*/
							if (count($condition)) {
								$condition['primary'] = '`primary` = :primary';
								$params[":primary"] = (($field == "url") ? 1 : 0);

								if ($field == "url") {
									$value = \runner::get_rewrite_url($value,
										$resource["resource_uri"], $resource["reference"], true);

									$SQL_GET = 'SELECT `rewrite_id` FROM `{PREFIX}rewrites` WHERE ';
									$SQL_GET .= implode(" AND ", $condition);
									if ($rewrite = \db::query($SQL_GET, $params)) {
										$SQL_UPDATE = 'UPDATE `{PREFIX}rewrites` SET `url` = :url WHERE ';
										$SQL_UPDATE .= '`rewrite_id` = :id';
										\db::query($SQL_UPDATE,
											array(":url" => $value, ":id" => $rewrite[0]["rewrite_id"]));
									} else {
										$SQL_UPDATE = 'INSERT INTO `{PREFIX}rewrites` (`url`';
										foreach (array_keys($condition) as $insert_field) {
											$SQL_UPDATE .= ', `' . $insert_field . '`';
										}
										$SQL_UPDATE .= ') VALUES (:url';
										foreach (array_keys($params) as $param_field) {
											$SQL_UPDATE .= ', ' . $param_field;
										}
										$SQL_UPDATE .= ')';
										$params[":url"] = $value;
										\db::query($SQL_UPDATE, $params);
									}

									$response["success"] = true;
								} else {
									$SQL_UPDATE = 'DELETE FROM `{PREFIX}rewrites` WHERE ';
									$SQL_UPDATE .= implode(" AND ", $condition);
									\db::query($SQL_UPDATE, $params);

									if ($urls = explode(PHP_EOL, $value)) {
										$SQL_UPDATE = 'INSERT INTO `{PREFIX}rewrites` (`url`';
										foreach (array_keys($condition) as $insert_field) {
											$SQL_UPDATE .= ', `' . $insert_field . '`';
										}
										$SQL_UPDATE .= ') VALUES (:url';
										foreach (array_keys($params) as $param_field) {
											$SQL_UPDATE .= ', ' . $param_field;
										}
										$SQL_UPDATE .= ')';

										foreach ($urls as $url) {
											if ($url) {
												$params[":url"] = \runner::get_rewrite_url($url,
													$resource["reference"], $resource["resource_uri"], true);
												\db::query($SQL_UPDATE, $params);
											}
										}
									}

									$response["success"] = true;
								}

							}

							if ($response["success"] && ($result_apply = \db::query($SQL_apply, $params_apply))) {
								$response["apply"] = $result_apply[0];
							}
							break;
						case "title":
						case "keywords":
						case "description":
							// model_metas table direct column
							$SQL = 'SELECT model_meta_id FROM {PREFIX}model_metas WHERE ' .
								'reference = :reference';
							$params = array(
								":reference" => $resource["reference"],
							);
							if ($result = \db::query($SQL, $params)) {
								$SQL_UPDATE = 'UPDATE `{PREFIX}model_metas` SET `' . $field .
									'` = :value WHERE `model_meta_id` = :id';
								$params_update = array(
									':value' => $value,
									':id' => $result[0]["model_meta_id"],
								);
								\db::query($SQL_UPDATE, $params_update);

								$response["success"] = true;
							} else {
								$SQL_UPDATE = 'INSERT INTO `{PREFIX}model_metas` (`reference`, `' . $field . '`) ' .
									'VALUES (:reference, :value)';
								$params_update = $params;
								$params_update[":value"] = $value;

								if ($model_meta_id = \db::insert($SQL_UPDATE, $params_update)) {
									$response["success"] = true;
								}
							}
							if ($response["success"] && ($result_apply = \db::query($SQL_apply, $params_apply))) {
								$response["apply"] = $result_apply[0];
							}
							break;
						default:
							// model_metas table meta field
							$SQL = 'SELECT model_meta_id, meta FROM {PREFIX}model_metas WHERE ' .
								'reference = :reference';
							$params = array(
								":reference" => $resource["reference"],
							);
							if ($result = \db::query($SQL, $params)) {
								if ($meta = json_decode($result[0]["meta"], true)) {
									$meta[$field] = $value;
								} else {
									$meta = array($field => $value);
								}
								$meta_value = json_encode($meta);

								$SQL_UPDATE = 'UPDATE `{PREFIX}model_metas` SET `meta` = ' .
									':value WHERE `model_meta_id` = :id';
								$params_update = array(
									':value' => $meta_value,
									':id' => $result[0]["model_meta_id"],
								);
								\db::query($SQL_UPDATE, $params_update);

								$response["success"] = true;
							} else {
								$meta = array($field => $value);
								$meta_value = json_encode($meta);

								$SQL_UPDATE = 'INSERT INTO `{PREFIX}model_metas` (`reference`, `meta`) ' .
									'VALUES (:reference, :value)';
								$params_update = $params;
								$params_update[":value"] = $meta_value;

								if ($model_meta_id = \db::insert($SQL_UPDATE, $params_update)) {
									$response["success"] = true;
								}
							}
							if ($response["success"] && ($result_apply = \db::query($SQL_apply, $params_apply))) {
								$response["apply"] = $result_apply[0];
							}
							break;
					}
				}
			}
		}

	}

	echo json_encode($response);
});