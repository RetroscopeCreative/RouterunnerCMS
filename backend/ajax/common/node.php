<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.10.30.
 * Time: 20:47
 */
session_start();

require $_SESSION["runner_config"]['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $_SESSION["runner_config"]["SITEROOT"] . $_SESSION["runner_config"]["BACKEND_ROOT"] . 'Routerunner/Routerunner.php';
use \Routerunner\Routerunner as runner;

$post = array_merge($_GET, $_POST);

new runner(array(
	'mode' => 'backend',
	'params' => $post,
	'silent' => true,
	'method' => 'any',
	'resource' => '/',
	'bootstrap' => false,
), function() use ($post) {
	$lang = \runner::config("language");
	$treeroot = false;
	$route = array('');
	if (isset($post["current"]) && is_numeric($post["current"])) {
		$lang = \Routerunner\Bootstrap::lang($post["current"]);
		$parents = \Routerunner\Bootstrap::parent($post["current"], $treeroot);
		if (!$treeroot && !empty($post['current'])) {
			if ($current_model = \model::load(array('direct' => $post['current']))) {
				if ($current_model->class == 'tree') {
					$treeroot = array(
						"lvl" => 1,
						"reference" => $current_model->reference,
						"model_class" => $current_model->class,
						"table_id" => $current_model->table_id,
					);
				}
			}
		}
		if ($treeroot) {
			$route[] = $treeroot["model_class"] . '/' . $treeroot["table_id"];
		}
		if (isset($post['route']) && is_array($post['route'])) {
			$treeroot_index = false;
			foreach ($post['route'] as $post_route_index => $post_route) {
				if ($post_route && ($post_route == $treeroot["model_class"]
					|| strpos($post_route, $treeroot["model_class"] . '/') !== false)) {
					$treeroot_index = $post_route_index;
					break;
				}
			}
			if ($treeroot_index && count($post['route']) > $treeroot_index) {
				$route = array_merge($route, array_slice($post['route'], $treeroot_index+1));
			}
		}
		$tree = \Routerunner\Bootstrap::getTree($post["current"]);
		$current = $tree["current"];
		//$route[] = $current["model_class"];
	}
	if ($treeroot && isset($treeroot["reference"])) {
		$treeroot = $treeroot["reference"];
	} else {
		$treeroot = $lang;
	}

	$context = array(
		'reference' => ((isset($post["reference"]) && $post["reference"]) ? $post["reference"] : $treeroot),
		'model_class' => '',
		'table_id' => 0,
		//'route' => ((isset($post['route']) && $post['route']) ? $post['route'] : array('')),
		'route' => ($route ? $route : array('')),
		'open' => true,
		//'movements' => \runner::config('movements'),
	);

	if (isset($post['reference'], $post['model_class'], $post['table_id']) && $post['reference']
		&& $post['model_class'] && $post['table_id']) {
		$context['reference'] = $post['reference'];
		$context['model_class'] = $post['model_class'];
		$context['table_id'] = $post['table_id'];
		$context['route'] = $route;
	}

	$router = false;
	$override = null;
	$root = \runner::config("BACKEND_DIR") . DIRECTORY_SEPARATOR . 'scaffold';
	echo \runner::route('/backend/tree/container', $context, $router, true, $override, $root);
});
