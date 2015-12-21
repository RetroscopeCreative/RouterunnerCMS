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
		"change_id" => false,
	);

	$SQL = "CALL {PREFIX}change_delete(:change_id)";
	$params = array(
		":change_id" => $post["change_id"],
	);
	if ($result = \db::query($SQL, $params)) {
		$response["success"] = true;
	}
	echo json_encode($response);
});