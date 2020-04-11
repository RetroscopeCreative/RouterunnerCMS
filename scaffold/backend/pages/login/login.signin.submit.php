<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.02.18.
 * Time: 14:55
 */

$post = $_POST;
$msg = "";
if (empty($post["email"]) || empty($post["password"]) || !logincrypt($post["email"], $post["password"], $msg)) {
    if (empty($msg)) {
        $msg = 'Unexpected error';
    }
	echo '<div class="alert alert-danger">' . $msg . '</div>';
} else {
	echo '<div class="alert alert-success">Logged in successfully!</div>';

	$SQL = "SELECT id, email, last_login, last_ip, licence FROM member WHERE email = :email";
	if ($result = \Routerunner\Db::query($SQL, array(":email" => $post["email"]))) {
		$user = $result[0];
		if (isset($post["rememberme"]) && $post["rememberme"]) {
			$user["rememberme"] = true;
		}
		\runner::flash('member', $user);

		$SQL = "UPDATE member SET last_login = :last_login, last_ip = :last_ip WHERE email = :email";
		$params = array(
			":last_login" => time(),
			":last_ip" => $_SERVER["REMOTE_ADDR"],
			":email" => $post["email"]
		);
		\Routerunner\Db::query($SQL, $params);

		\runner::redirect($_SERVER["HTTP_REFERER"]);
	}
}
