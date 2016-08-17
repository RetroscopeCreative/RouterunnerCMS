<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.10.18.
 * Time: 15:08
 */
require '../runner-config.php';

require '../Routerunner/Routerunner.php';
use \Routerunner\Routerunner as runner;

$post = $_POST;

$scaffold = '../scaffold';
$tree = (@include $scaffold . '/model/tree.php');


$runner = runner::runnerParams("runner", array("params" => $post));
if (isset($_SESSION["runner"])) {
	$runner = json_decode(base64_decode($_SESSION["runner"]), true);
}
if (!is_array($runner)) {
	$runner = array();
}

$runner = array_merge(array(
	'root' => 'desktop',
	'SITE' => '',
	'SITENAME' => '',
	'BASE' => ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/',

	'tree' => $tree,
	'language' => 1,
	'mode' => 'backend',
	'params' => $post,
), $runner);


new runner($runner, function() use ($post) {
	$router = false;
	$route = "/backend/panel/pageproperties";
	echo \runner::route($route, null, $router, true);
});
