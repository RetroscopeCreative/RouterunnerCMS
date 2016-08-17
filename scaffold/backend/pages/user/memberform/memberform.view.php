<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.07.13.
 * Time: 11:11
 */
$bootstrap = \bootstrap::get();
$params = $bootstrap->params;
if (\context::get("profile") && \context::get("profile") === \user::me()) {
	if (!is_array($params)) {
		$params = array();
	}
	$params["id"] = \context::get("profile");
}

if ($params && isset($params["id"])) {
	echo '<div class="row client-row" style="padding: 30px; background-color: #ccc;">' . PHP_EOL;
	echo \Routerunner\Routerunner::form("frm", $runner, true);
	echo '</div>' . PHP_EOL;
}
