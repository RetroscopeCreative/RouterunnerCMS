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
$draft = (isset($_POST['draft']) ? $_POST['draft'] : false);

new runner(array(
	'root' => 'standard',
	'SITE' => 'appetizer',
	'SITENAME' => 'appetizer',
	'BASE' => 'http://' . $_SERVER['HTTP_HOST'] . '/appetizer/',

	'mode' => 'draft',
	'reference' => $reference,
	'draft' => $draft,
), function() use ($route) {
	echo \runner::route($route, null, $router, true);
});