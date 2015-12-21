<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.10.23.
 * Time: 16:23
 */

$confirm_code = \context::get("confirm_code");
$email = \context::get("email");

$address = $email;
$header = array(
	"Subject" => "Forgotten password",
	"FromName" => "Appetizer",
);