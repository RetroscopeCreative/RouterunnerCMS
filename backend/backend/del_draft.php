<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.09.10.
 * Time: 15:43
 */
require '../../Routerunner/Routerunner.php';
use \Routerunner\Routerunner as runner;

$reference = (isset($_POST['reference']) ? $_POST['reference'] : false);
$draft = (isset($_POST['draft']) ? $_POST['draft'] : false);

new runner(function() use ($reference, $draft) {
	$SQL = 'DELETE FROM {PREFIX}drafts WHERE id = :id AND reference = :reference';
	\db::query($SQL, array(':id' => $draft, ':reference' => $reference));
});