<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.10.23.
 * Time: 16:23
 */

//$newpwd = \context::get("pwd");
$email = \context::get("email");

$address = $email;
$header = array(
	"Subject" => "New password",
	"FromName" => "Appetizer",
);