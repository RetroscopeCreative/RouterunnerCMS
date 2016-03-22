<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.07.13.
 * Time: 11:38
 */

$return_SQL = true;
if ($succeed = \Routerunner\Form::submit($runner->form, $errors, $return_SQL, $return_params)) {
	$saved = false;
	if (isset($return_params[":nonce"], $_SESSION["nonce"]) && \Routerunner\Crypt::checker($return_params[":nonce"], $_SESSION["nonce"])) {
		unset($_SESSION["nonce"]);

		$pwd_change = false;
		if ($return_params[":pwd"] && $return_params[":pwd_confirm"]
			&& $return_params[":pwd"] === $return_params[":pwd_confirm"]) {
			$return_params[":pwd"] = pwd($return_params[":email"], $return_params[":pwd"]);
			$pwd_change = true;
		} elseif (($return_params[":pwd"] || $return_params[":pwd_confirm"])
			&& $return_params[":pwd"] !== $return_params[":pwd_confirm"]) {
			$errors["pwd_confirm"] = "Passwords not equals!";
		}

		$is_insert = false;
		$name = $return_params[":name"];
		$usergroup = $return_params[":usergroup"];
		if (strpos($return_SQL, "INSERT") === 0) {
			$return_SQL = str_replace(
				array("`nonce`, ", "`id`, ", ", `usergroup`", ", `name`", ", `pwd_confirm`"),
				"", $return_SQL);
			$return_SQL = str_replace(array(":nonce, ", ":id, ", ", :usergroup", ", :name", ", :pwd_confirm"),
				"", $return_SQL);
			if (!$pwd_change) {
				$return_SQL = str_replace(", `pwd`", "", $return_SQL);
				$return_SQL = str_replace(", :pwd", "", $return_SQL);
			}
			unset($return_params[":id"]);
			$is_insert = true;
		} else {
			$return_SQL = str_replace(
				array("`nonce` = :nonce, ", ", `usergroup` = :usergroup", ", `name` = :name",
					", `pwd_confirm` = :pwd_confirm"), "", $return_SQL);
			if (!$pwd_change) {
				$return_SQL = str_replace(", `pwd` = :pwd", "", $return_SQL);
			}
		}
		unset($return_params[":nonce"], $return_params["usergroup"], $return_params["name"],
			$return_params[":pwd_confirm"]);
		if (!$pwd_change) {
			unset($return_params[":pwd"]);
		}

		if ($return_params[":licence"]) {
			$return_params[":licence"] = strtotime($return_params[":licence"]);
		}

		if ($is_insert) {
			if ($id = \db::insert($return_SQL, $return_params)) {
				$saved = true;
			}
		} else {
			$id = $return_params[':id'];
			\db::query($return_SQL, $return_params);
			$saved = true;
		}
		if ($saved) {

			$scopes = false;
			if (isset($return_params[':scope'])) {
				$scopes = explode(',', $return_params[':scope']);

				$perm_SQL = 'SELECT user_id FROM `{PREFIX}user` WHERE email = :email LIMIT 1';
				if ($uid_result = \db::query($perm_SQL, array(':email' => $return_params[':email']))) {
					$uid = $uid_result[0]['user_id'];

					$perm_SQL = 'DELETE FROM `{PREFIX}permissions` WHERE `owner` = :uid';
					\db::query($perm_SQL, array(':uid' => $uid));

					$SQL = "SELECT id, label FROM menu ORDER BY id";
					if ($result = \db::query($SQL)) {
						foreach ($result as $row) {
							$perm_SQL = "
	INSERT INTO `{PREFIX}permissions` (`reference`, `owner`, `group`, `other`, `permission`)
	SELECT reference, :uid, :gid, 0, :perm FROM `{PREFIX}models` WHERE table_from = 'menu' AND table_id = :scope LIMIT 1";
							\db::query($perm_SQL, array(
								':scope' => $row['id'],
								':uid' => $uid,
								':gid' => $usergroup,
								':perm' => (in_array($row['id'], $scopes) ? 63 : 2),
							));
						}
					}
				}
			}

			$user_SQL = "SELECT u.user_id, u.name, u.usergroup FROM {PREFIX}user AS u WHERE u.email = :email";
			$user_params = array(
				":email" => $return_params[":email"],
				":name" => $name,
				":usergroup" => $usergroup,
			);
			if ($user_result = \db::query($user_SQL, array(":email" => $return_params[":email"]))) {
				$user_result = $user_result[0];
				if (\context::get("profile") && \context::get("profile") === \user::me()) {
					$user_SQL = "UPDATE `{PREFIX}user` SET name = :name WHERE email = :email";
					\db::query($user_SQL, $user_params);
				} elseif ($user_result["name"] != $name || $user_result["usergroup"] != $usergroup) {
					$user_SQL = "UPDATE `{PREFIX}user` SET name = :name, usergroup = :usergroup WHERE email = :email";
					\db::query($user_SQL, $user_params);
				}
			} else {
				if (\context::get("profile") && \context::get("profile") === \user::me()
					&& !is_numeric($user_params[":usergroup"])) {
					$user_params[":usergroup"] = 0;
				}
				$user_SQL = "INSERT INTO `{PREFIX}user` (`email`, `name`, `usergroup`) VALUES (:email, :name, :usergroup)";
				\db::insert($user_SQL, $user_params);
			}
		}

		if ($saved) {
			$url = \bootstrap::get("url");
			echo <<<HTML
<h1 class="client-form-success text-success">Saved successfully!</h1>
<script>
	setTimeout(function() {
		window.location.href = "admin/{$url}";
	}, 1000);
</script>
HTML;
		}
	}
	if (!$saved) {
		echo '	<h1 class="text-danger">Unexpected error or authentication problem happened!</h1>';
	}

	if ($errors) {
		echo '	<h1 class="text-danger">Error happened!</h1>';
		foreach ($errors as $field => $row) {
			echo '<!--' . $field . '//-->' . PHP_EOL;
			echo $row;
		}
	}

} else {
	echo '	<h1 class="text-danger">Error happened!</h1>';
	if ($errors) {
		foreach ($errors as $field => $row) {
			echo '<!--' . $field . '//-->' . PHP_EOL;
			echo $row;
		}
	}
}