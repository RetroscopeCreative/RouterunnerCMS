<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.10.20.
 * Time: 10:33
 */

header('Content-Type: application/json');

$require = '../../../';
try {
    $require = \runner::config('SITEROOT') . \runner::config('BACKEND_ROOT');
} catch (Exception $e) {

}
if (!class_exists('\Routerunner\Routerunner', false)) {
    require $require . 'Routerunner/Routerunner.php';
}
use \Routerunner\Routerunner as runner;

$post = array_merge($_GET, $_POST);

new runner(array(
	'mode' => 'backend',
	'params' => $post,
	'method' => 'post',
	'resource' => '/',
	'bootstrap' => false,
), function() use ($post) {
	$settings = \Routerunner\Routerunner::$static->settings;
	\Routerunner\Bootstrap::initialize($settings, true);

    $change_id = (is_numeric($post["change_id"]) ? $post["change_id"] : 0);

    include \runner::config('SITEROOT') . \runner::config('BACKEND_ROOT') . 'backend' . DIRECTORY_SEPARATOR .
        'include' . DIRECTORY_SEPARATOR . 'apply_change.php';

	echo json_encode($response);
});