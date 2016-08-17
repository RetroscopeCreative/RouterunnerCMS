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

echo "";
die();
echo "<h1>history LOADED</h1>";
echo "<div>";
echo print_r($post, true);
echo "</div>";

/*

new runner(array(
	'root' => 'standard',
	'SITE' => 'appetizer',
	'SITENAME' => 'appetizer',
	'BASE' => 'http://' . $_SERVER['HTTP_HOST'] . '/appetizer/',

	'mode' => 'cms',
	'params' => $post,
), function() use ($post) {
	$router = false;
	/*$context = $post;
	$html = runner::route("/backend/panel/pageproperties", $context, $router, true);

	$debug = 1;
	echo $html;
	*/
/*
	$route = "/backend/panel/pageproperties";
	echo \runner::route($route, null, $router, true);
});

/*
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
*/