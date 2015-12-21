<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.08.23.
 * Time: 20:59
 */
 
function pwd($email, $pwd) {
	$input = $email . ";" . $pwd;

	$unique_salt = \runner::config("pwd_salt");
	$unique_logarithm = \runner::config("pwd_logarithm");
	$unique_method = \runner::config("pwd_method");

	return \Routerunner\Crypt::crypter($input, null, null, 0, $unique_salt, $unique_logarithm, $unique_method);
};