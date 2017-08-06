<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.10.23.
 * Time: 16:23
 */

if (\bootstrap::get("url") == "forgotten") {
	$isOk = false;
	$bootstrap = \bootstrap::get();
	if (isset($bootstrap->params) && is_array($bootstrap->params) && count($bootstrap->params) === 1) {
		$params = explode("/", array_shift(array_keys($bootstrap->params)));
		$SQL = "SELECT id, email, confirm_date, licence FROM member WHERE id = :id";
		if ($result = \Routerunner\Db::query($SQL, array(":id" => $params[0]))) {
			$user = $result[0];

			$secret = $params[1];
			$hash = $params[2];

			$SQL_Crypt = 'SELECT hash FROM {PREFIX}crypt WHERE secret = :secret AND keep > UNIX_TIMESTAMP()';
			$params_Crypt = array(':secret' => $hash);
			if ($result_Crypt = \db::query($SQL_Crypt, $params_Crypt)) {

				$crypt_hash = $result_Crypt[0]['hash'];
				$confirm = 'forgotten/' . implode('/', $user) . '/' . $secret;

				if (\Routerunner\Crypt::checker($confirm, $crypt_hash, $secret)) {
					//\Routerunner\Crypt::delete_crypt($crypt_hash, $confirm);

					$alphabet = "abcdefghijklmnpqrstuwxyzABCDEFGHIJKLMNPQRSTUWXYZ123456789";
					$pwd = "";
					for ($i = 0; $i < 8; $i++) {
						$n = rand(0, strlen($alphabet)-1);
						$pwd .= substr($alphabet, $n, 1);
					}
					$user['pwd'] = $pwd;

					$input = $user["email"] . ";" . $pwd;
					$unique_salt = \runner::config('pwd_salt');
					$unique_logarithm = \runner::config('pwd_logarithm');
					$unique_method = \runner::config('pwd_method');

					$pwd_to_store = \Routerunner\Crypt::crypter($input, null, null, 0, $unique_salt, $unique_logarithm, $unique_method);

					if (\Routerunner\Mail::mailer('/mail/newpwd', $user)) {
						$SQL = 'UPDATE member SET pwd = :pwd WHERE id = :id AND email = :email';
						$params = array(
							':pwd' => $pwd_to_store,
							':id' => $user['id'],
							':email' => $user['email'],
						);
						\db::query($SQL, $params);
						$isOk = true;
					}
				}
			}
		}
	}
	if ($isOk) {
		\runner::now("newpwd", "succeed");
	} else {
		\runner::now("newpwd", "error");
	}
}