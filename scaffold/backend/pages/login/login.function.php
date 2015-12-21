<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.02.18.
 * Time: 14:55
 */
function logincrypt($email, $pwd, & $error=false)
{
	$isOk = false;

	$unique_salt = \runner::config("pwd_salt");
	$unique_logarithm = \runner::config("pwd_logarithm");
	$unique_method = \runner::config("pwd_method");
	$input = $email . ";" . $pwd;

	//var_dump(\Routerunner\Crypt::crypter($input, null, null, 0, $unique_salt, $unique_logarithm, $unique_method));

	$SQL = "SELECT pwd, confirm_date FROM member WHERE email = :email";
	if ($result = \Routerunner\Db::query($SQL, array(":email" => $email))) {
		$result = $result[0];
		if (is_null($result["confirm_date"])) {
			$error = "User has not been confirmed!";
		}
		$isOk = \Routerunner\Crypt::checker($input, $result["pwd"], $unique_salt, $unique_logarithm, $unique_method);
		if (!$isOk) {
			$error = "Incorrect password!";
		}
	} else {
		$error = "User is not exists!";
	}
	return $isOk;
}