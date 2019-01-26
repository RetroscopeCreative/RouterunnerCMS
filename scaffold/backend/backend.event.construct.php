<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.10.23.
 * Time: 16:23
 */

\runner::stack("session_id", false, true);

$bootstrap = \bootstrap::get();

$email=null;
$name=null;
$group=null;
$_custom=array();
$_scope=null;
$_auth=null;

if ((!user::me($email, $name, $group, $_custom, $_scope, $_auth)) || (empty($group) || (string) $group !== "1")) {
	if (user::me()) {
		\user::logout();
	}
	switch ($bootstrap->url) {
		case "forgotten":
			//\runner::now("page", "pages/forgotten");
			//break;
		default:
			\runner::now("page", "pages/login");
			break;
	}
} else {
	$mainframe = ((strpos($bootstrap->url, "/") !== false)
		? substr($bootstrap->url, 0, strpos($bootstrap->url, "/")) : $bootstrap->url);
	$subframe = ((strpos($bootstrap->url, "/") !== false)
		? substr($bootstrap->url, strpos($bootstrap->url, "/")+1) : false);
	switch ($mainframe) {
		case "newsletter":
		case "user":
			\runner::now("page", "pages/" . $mainframe);
			\runner::now("subpage", $subframe);
			break;
		default:
			$mainframe = "editor";
			\runner::now("page", $mainframe);
			break;
	}

	$granted = \user::auth($mainframe, $subframe);

	if (!$granted) {
		\runner::now("page", "pages/restricted");
		\runner::now("subpage", null);
	}
}