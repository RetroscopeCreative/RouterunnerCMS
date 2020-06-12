<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.02.18.
 * Time: 14:55
 */

$post = $_POST;
$msg = "";
$SQL = "SELECT id, email, confirm_date, licence FROM member WHERE email = :email";
if (!empty($post["email"]) && ($result = \Routerunner\Db::query($SQL, array(":email" => $post["email"])))) {
	$user = $result[0];
	if (is_null($user["confirm_date"])) {
		$msg = "User has not been confirmed!";
	}
	if (!$msg) {
		// confirm generálás
		$secret = uniqid(md5(uniqid('', true)));
		$confirm = 'forgotten/' . implode('/', $user) . '/' . $secret;
		$expire = time() + 2*24*60*60;
		$confirm_hash = \Routerunner\Crypt::crypter($confirm, $expire, $user['id'], 0, $secret);

		$path = runner::config("BASE") . 'admin/forgotten/?' . $user['id'] . '/' . $secret . '/' . $confirm_hash;

		$user["confirm_code"] = $path;
		if ($result = \mail::mailer("/mail/forgotten", $user, null)) {
			$debug = 1;
		} else {
			$msg = "E-mail cannot be sent!";
		}
	}
} else {
	$msg = "User is not exists!";
}


if ($msg) {
	echo '<div class="alert alert-forgotten alert-danger">' . $msg . '</div>';
} else {
	echo '<div class="alert alert-forgotten alert-success">New password confirmation has been sent to your e-mail address!</div>';
}
