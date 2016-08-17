<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.09.08.
 * Time: 15:33
 */
require '../../Routerunner/Routerunner.php';
use \Routerunner\Routerunner as runner;

$draft = (isset($_POST['draft']) ? $_POST['draft'] : false);
$reference = (isset($_POST['reference']) ? $_POST['reference'] : false);

$response = array(
	'success' => false,
	'response' => 'Error!',
);

new runner(function() use ($reference, $draft) {
	$SQL = <<<SQL
INSERT INTO {PREFIX}drafts (`reference`, `date`, `user`, `model`)
VALUES (:reference, :date, :user, :model)
SQL;
	$params = array(
		':reference' => $reference,
		':date' => time(),
		':user' => 1,
		':model' => json_encode($draft)
	);
	\db::query($SQL, $params);

	$response = array(
		'success' => true,
		'response' => 'Successfully saved to drafts!',
	);

	echo json_encode($response);

});