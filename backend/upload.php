<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.02.20.
 * Time: 14:27
 */

$debug = 1;

$return = array(
	"success" => false,
	"file" => "",
);

$ds = DIRECTORY_SEPARATOR;
$storeFolder = 'uploads';
if (!empty($_FILES)) {
	$tempFile = $_FILES['file']['tmp_name'];
	$targetPath = str_replace($ds."backend", "", dirname( __FILE__ )) . $ds. $storeFolder . $ds;
	$targetFile =  $targetPath. $_FILES['file']['name'];
	move_uploaded_file($tempFile,$targetFile);
	$return["success"] = true;
	$return["file"] = $targetFile;
}
echo json_encode($return);
