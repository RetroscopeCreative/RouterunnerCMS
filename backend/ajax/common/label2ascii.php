<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.02.26.
 * Time: 20:39
 */

session_start();

require $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $_SESSION["runner_config"]["SITEROOT"] . $_SESSION["runner_config"]["BACKEND_ROOT"] . 'Routerunner/Routerunner.php';
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
	$response = array("ascii" => null);

	if (isset($post["str"])) {
		$ascii = \runner::toAscii(strip_tags($post["str"]));
	}
	if (isset($post["reference"])) {
		$SQL = "SELECT rewrite_id, reference FROM `{PREFIX}rewrites` WHERE (url = :url OR resource_uri = :url) AND (reference IS NULL OR reference <> :reference)";
		$params = array(
			":url" => trim($ascii),
			":reference" => $post["reference"],
		);
		if (\db::query($SQL, $params)) {
			$ascii .= "-" . strftime("%Y%m%d-%H%M%S");
		}
	}

	$response["ascii"] = $ascii;

	echo json_encode($response);
});

