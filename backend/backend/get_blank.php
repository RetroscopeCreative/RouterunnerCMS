<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.09.25.
 * Time: 15:01
 */
require '../../Routerunner/Routerunner.php';
use \Routerunner\Routerunner as runner;

$route = (isset($_POST['route']) ? $_POST['route'] : false);
$reference = (isset($_POST['reference']) ? $_POST['reference'] : false);

new runner(array(
	'root' => 'standard',
	'SITE' => 'appetizer',
	'SITENAME' => 'appetizer',
	'BASE' => 'http://' . $_SERVER['HTTP_HOST'] . '/appetizer/',

	'mode' => 'blank',
), function() use ($route, $reference) {
	$context = array('self' => array('reference' => $reference));

	//$model = \model::load($context, $route, $router);

	//$router->runner->model = $model;
	//$router->runner->path = substr($route, 0, strrpos($route, '/'));
	//$router->runner->route = substr($route, strrpos($route, '/'));

	$echo = \runner::route($route, $context, $router, true, true);
	echo $echo;
});