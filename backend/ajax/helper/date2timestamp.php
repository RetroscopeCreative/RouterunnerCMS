<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.02.26.
 * Time: 20:39
 */

$post = $_POST;

$response = array("timestamp" => null);

if (isset($post["date"])) {
	$date = $post["date"];
	$date = str_replace(". ", " ", $date);
	$date = str_replace(".", "-", $date);
	if ($timestamp = strtotime($date)) {
		$response["timestamp"] = $timestamp;
	}
}

echo json_encode($response);