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
		if (strpos($return_SQL, "INSERT") === 0) {
			if ($id = \db::insert($return_SQL, $return_params)) {
				$saved = true;
				$SQL = "UPDATE e_cron SET start = :time WHERE id = :id";
				\db::query($SQL, array(":time" => time(), ":id" => $id));
			}
		} else {
			\db::query($return_SQL, $return_params);
			$saved = true;
		}
	}
	if ($saved) {
		$url = \bootstrap::get("url");
		echo <<<HTML
<h1 class="client-form-success text-success">Sikeresen elmentve!</h1>
<script>
	setTimeout(function() {
		window.location.href = "admin/{$url}";
	}, 1000);
</script>
HTML;
	} else {
		echo '	<h1 class="text-danger">Ismeretlen vagy jogosultsági hiba történt!</h1>';
	}
} else {
	echo '	<h1 class="text-danger">Hiba történt!</h1>';
	if ($errors) {
		foreach ($errors as $field => $row) {
			echo '<!--' . $field . '//-->' . PHP_EOL;
			echo $row;
		}
	}
}