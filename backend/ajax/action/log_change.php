<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.10.20.
 * Time: 10:33
 */
header('Content-Type: application/json');

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

	$SQL = "CALL {PREFIX}change_log(:change_id, :session, :reference, :resource, :changes, :state)";
	$params = array(
		":change_id" => (is_numeric($post["change_id"]) ? $post["change_id"] : null),
		":session" => \runner::stack("session_id"),
		":reference" => $post["reference"],
		":resource" => json_encode($post["resource"]),
		":changes" => json_encode($post["changes"]),
		":state" => $post["state"],
	);
	if ($result = \db::query($SQL, $params)) {
		$response["success"] = true;
		$response["change_id"] = $result[0]["change_id"];
		$response["date"] = $result[0]["date"];
		$response["state"] = $result[0]["state"];
	}
	echo json_encode($response);
});