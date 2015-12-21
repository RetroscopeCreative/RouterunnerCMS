<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.07.13.
 * Time: 11:27
 */

$debug = 1;

$id = 0;
$email = false;
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
	$id = $_GET["id"];
} elseif (\context::get("profile") && \context::get("profile") === \user::me($email)) {
	$SQL = "SELECT id FROM member WHERE email = :email";
	if ($result = \db::query($SQL, array(":email" => $email))) {
		$id = $result[0]["id"];
	}
}

$form = array(
	'method' => 'post',
	'xmethod' => ($id ? 'put' : 'post'),
	'name' => 'member',
	'error_format' => '<p class="err">%s</p>'.PHP_EOL,
	'from' => 'member',
	'condition' => array(
		array('member.id = :id', array(':id'=>'id'), 'AND'),
	),
);

$nonce = uniqid(rand(0, 1000000));
if (!isset($_POST["nonce"])) {
	$_SESSION["nonce"] = \Routerunner\Crypt::crypter($nonce);
}

$usergroups = array();
$SQL = "SELECT usergroup_id, label FROM {PREFIX}usergroup ORDER BY usergroup_id";
if ($result = \db::query($SQL)) {
	foreach ($result as $row) {
		$usergroups[$row["usergroup_id"]] = $row["label"];
	}
}

$value = array(
	"email" => "",
	"name" => "",
	"reg_date" => time(),
	"confirm_date" => time(),
	"licence" => "",
	"usergroup" => "",
);

if ($id) {
	$SQL = <<<SQL
SELECT member.email, u.name, member.reg_date, member.confirm_date, member.licence, u.usergroup
FROM `member`
LEFT JOIN {PREFIX}user as u ON u.email = member.email
WHERE member.id = ?

SQL;

	if ($result = \db::query($SQL, array($id))) {
		$value = array_merge($value, $result[0]);
	}
}

$input = array(
	'id' => array(
		'type' => 'hidden',
		'field' => 'id',
		'value' => ($id ? $id : "")
	),
	'nonce' => array(
		'type' => 'hidden',
		'field' => 'nonce',
		'value' => $nonce
	),
	'reg_date' => array(
		'type' => 'hidden',
		'field' => 'reg_date',
		'value' => $value["reg_date"]
	),
	'confirm_date' => array(
		'type' => 'hidden',
		'field' => 'confirm_date',
		'value' => $value["confirm_date"]
	),
	'email' => array(
		'type' => (\context::get("profile") ? 'label' : 'text'),
		'field' => 'email',
		'label' => 'E-mail cím',

		'input-id' => 'frm-email',
		'class' => '',

		'value' => $value["email"]
	),
	'name' => array(
		'type' => 'text',
		'field' => 'name',
		'label' => 'Name',

		'input-id' => 'frm-name',
		'class' => '',

		'value' => $value["name"]
	),
	'pwd' => array(
		'type' => 'password',
		'field' => 'pwd',
		'label' => 'Password (leave empty if not to change)',

		'input-id' => 'frm-pwd',
		'class' => '',

		'value' => ''
	),
	'pwd_confirm' => array(
		'type' => 'password',
		'field' => 'pwd_confirm',
		'label' => 'Password confirmation',

		'input-id' => 'frm-pwd_confirm',
		'class' => '',

		'value' => ''
	),

	'licence' => array(
		'type' => (\context::get("profile") ? 'label' : 'date'),
		'field' => 'licence',
		'label' => 'Licence',

		'input-id' => 'frm-licence',
		'class' => '',

		'value' => ((\context::get("profile") && $value["licence"]) ? strftime("%Y-%m-%d %H:%M:%S", $value["licence"]) : $value["licence"])
	),
	'usergroup' => array(
		'type' => (\context::get("profile") ? 'label' : 'select'),
		'field' => 'usergroup',
		'label' => 'Usergroup',

		'input-id' => 'frm-usergroup',
		'class' => '',

		'options' => $usergroups,

		'value' => (\context::get("profile") ? $usergroups[$value["usergroup"]] : $value["usergroup"]),
	),
	'submit' => array(
		'type' => 'submit',
		'input-id' => 'frm-submit',
		'value' => 'Mentés'
	),
);
$debug = 1;