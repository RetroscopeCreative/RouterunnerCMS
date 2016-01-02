<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.10.20.
 * Time: 10:33
 */
session_start();

require $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $_SESSION["runner_config"]["SITEROOT"] . $_SESSION["runner_config"]["BACKEND_ROOT"] . 'Routerunner/Routerunner.php';
use \Routerunner\Routerunner as runner;

$post = array_merge($_GET, $_POST);

new runner(array(
	'mode' => 'backend',
	'params' => $post,
	'method' => 'any',
	'resource' => '/',
	'bootstrap' => false,
), function() use ($post) {
	$router = false;
	$route = "/backend/model/remove";
	$override = null;
	$root = \runner::config("BACKEND_DIR") . DIRECTORY_SEPARATOR . 'scaffold';
	echo \runner::route($route, $post, $router, true, $override, $root);
});