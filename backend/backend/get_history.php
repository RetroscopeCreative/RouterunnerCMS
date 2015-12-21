<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.09.08.
 * Time: 15:33
 */
require '../../Routerunner/Routerunner.php';
use \Routerunner\Routerunner as runner;

$route = (isset($_POST['route']) ? $_POST['route'] : false);
$reference = (isset($_POST['reference']) ? $_POST['reference'] : false);
$history = (isset($_POST['history']) ? $_POST['history'] : false);

new runner(array(
	'root' => 'standard',
	'SITE' => 'appetizer',
	'SITENAME' => 'appetizer',
	'BASE' => 'http://' . $_SERVER['HTTP_HOST'] . '/appetizer/',

	'mode' => 'history',
	'reference' => $reference,
	'history' => $history,
), function() use ($reference, $route) {
	$context = array('self' => array('reference' => $reference));
	echo \runner::route($route, $context, $router, true);
});