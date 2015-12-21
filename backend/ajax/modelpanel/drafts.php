<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.10.20.
 * Time: 10:33
 */
require '../../../Routerunner/Routerunner.php';
use \Routerunner\Routerunner as runner;

$post = $_POST;
/*

echo "<h1>drafts LOADED</h1>";
echo "<div>";
echo print_r($post, true);
echo "</div>";
*/
new runner(array(
	'mode' => 'backend',
	'params' => $post,
), function() use ($post) {
	$router = false;
	$route = "/backend/model/drafts";
	$override = null;
	$root = 'Routerunner' . DIRECTORY_SEPARATOR . 'scaffold';
	echo \runner::route($route, $post, $router, true, $override, $root);
});
