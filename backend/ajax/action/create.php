<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.11.03.
 * Time: 14:47
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
	'silent' => true,
	'params' => $post,
	'method' => 'post',
	'resource' => '/',
	'bootstrap' => false,
), function() use ($post) {

	$return = array(
		'success' => false,
		'html' => '',
		'html_before' => '',
		'html_after' => '',
		'backend_context' => '',
		'reference' => false,
		'model' => false,
	);

	$scaffold = \Routerunner\Helper::$scaffold_root;
	$_route = (isset($post['route']) ? $post['route'] : '');
	if ($model_class = (isset($post['class']) ? $post['class'] :
		(isset($post['model_class']) ? $post['model_class'] : false))) {
		$route = '/model/' . $model_class;
		if (file_exists($scaffold . $_route . DIRECTORY_SEPARATOR . $model_class . '.runner.php')) {
			$route = $_route;
		} elseif (file_exists($scaffold . $_route . DIRECTORY_SEPARATOR . $model_class .
			DIRECTORY_SEPARATOR . $model_class . '.runner.php')) {
			$route = $_route . DIRECTORY_SEPARATOR . $model_class;
		} elseif (file_exists($scaffold . $_route . DIRECTORY_SEPARATOR . $model_class .
			DIRECTORY_SEPARATOR . $model_class . '.runner.php')) {
			$route = $_route . DIRECTORY_SEPARATOR . $model_class;
		}
		$model = $post;

		$model["id"] = -1;
		$SQL = 'SELECT MIN(table_id) - 1 AS table_id FROM `{PREFIX}models` WHERE model_class = :class AND table_id < 0';
		if (($result = \db::query($SQL, array(":class" => $model_class))) && is_numeric($result[0]["table_id"])) {
			$model["id"] = $result[0]["table_id"];
		}

		$model["create"] = $model["id"];
		//unset($model['id'], $model['reference'], $model['route']);
		if (file_exists($scaffold . $route . DIRECTORY_SEPARATOR . $model_class . '.runner.php')) {
			$router = false;
			\runner::stack("model_create", array("route" => $route));
			\runner::route($route, array("direct" => 0),
				$router, true, $model);
			$return["html"] = $router->runner->html_render;
			$return["html_before"] = $router->runner->html_before;
			$return["html_after"] = $router->runner->html_after;
			if (isset($router->runner->backend_context["model"])) {
				$return["backend_context"] = $router->runner->backend_context["model"];
			}

			\runner::stack("model_create", false);

			$models_created = \runner::stack("models_created");
			if (!$models_created) {
				$models_created = array();
			}
			$parent = array();
			if (isset($post["parent"])) {
				$parents = \Routerunner\Bootstrap::parent($post["parent"]);
				foreach ($parents as $cur_parent) {
					$parent[] = $cur_parent["reference"];
				}
				$parent[] = $post["parent"];
			}
            if (!isset($model->backend_ref) && \runner::stack('frontend_create')) {
                $model->backend_ref = \runner::stack('frontend_create');
            }
			$models_created[$model->reference] = array(
				"class" => $model->class,
				"route" => $model->route,
				"table_from" => $model->table_from,
				"table_id" => $model->table_id,
				"backend_ref" => $model->backend_ref,
				"parent" => $parent,
				"permission" => $model->permission,
			);
			\runner::stack("models_created", $models_created, true);

			$return['success'] = true;
			if (isset($model->reference)) {
				$return['reference'] = $model->reference;
				$return['model'] = $model;
			} elseif (is_array($model) && count($model) == 1) {
				$return['reference'] = $model[0]->reference;
				$return['model'] = $model[0];
			} elseif (is_array($model) && count($model) > 1) {
				$return['reference'] = array();
				foreach ($model as $row) {
					if (isset($row->reference)) {
						$return['reference'][] = $row->reference;
					}
				}
				$return['model'] = $model;
			}

			echo json_encode($return, JSON_HEX_QUOT);
		}
	}
});
