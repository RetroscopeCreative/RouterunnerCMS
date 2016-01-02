<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.06.05.
 * Time: 21:43
 */

require '../runner-config.php';

require '../' . \runner::config("BACKEND_DIR") . '/Routerunner/Routerunner.php';
use \Routerunner\Routerunner as runner;

$files = array_merge($_FILES);

new runner(array(
	'mode' => 'backend',
	'params' => $files,
	'method' => 'post',
	'resource' => '/',
	'bootstrap' => false,
), function() use ($files) {

	//require_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $_SESSION["runner_config"]["SITEROOT"] . $_SESSION["runner_config"]["BACKEND_ROOT"] . 'backend/thirdparty/' . 'DiacriticsRemovePHP/diacriticsRemove.php');
	if (!empty($files)) {
		$tempFile = $files['file']['tmp_name'];
		$targetPath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
		//$targetFilename = str_replace(array(" ", "+"), "-", removeDiacritics($files['file']['name']));
		$targetFilename = \runner::toAscii($files['file']['name']);
		$targetFile = $targetPath . $targetFilename;
		move_uploaded_file($tempFile, $targetFile);
	}

	echo $targetFilename;
});