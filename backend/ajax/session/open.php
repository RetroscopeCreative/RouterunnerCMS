<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.10.23.
 * Time: 17:31
 */
session_start();

require $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $_SESSION["runner_config"]["SITEROOT"] . $_SESSION["runner_config"]["BACKEND_ROOT"] . 'Routerunner/Routerunner.php';
use \Routerunner\Routerunner as runner;

new runner(array(
	'mode' => 'backend',
	//'method' => 'any',
	//'resource' => '/',
	'bootstrap' => false,
), function() {
	$debug = 1;
	$response = array(
		'session_id' => false,
		'session_open_date' => false,
	);
	if ($session_id = \runner::stack('session_id')) {
		$response['session_id'] = $session_id;
	} else {
		$token = \user::token();

		$SQL = 'CALL `{PREFIX}session_open`(0, :label, :token)';
		if ($session_result = \db::query($SQL, array(
			':label' => NULL,
			':token' => $token,
		))
		) {
			$response['session_id'] = $session_result[0]['session_opened'];
			$response['session_open_date'] = time();

			\runner::stack("models_created", array(), true);

			\runner::stack('session_id', $response['session_id'], true);
			//\runner::stack('session_id', 1, true);
		}
	}
	echo json_encode($response);
});