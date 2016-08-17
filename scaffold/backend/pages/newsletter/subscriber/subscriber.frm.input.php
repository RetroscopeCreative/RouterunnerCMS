<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.07.13.
 * Time: 11:27
 */


$form = array(
	'method' => 'post',
	'xmethod' => (isset($_GET["id"]) ? 'put' : 'post'),
	'name' => 'e_subscriber',
	'error_format' => '<p class="err">%s</p>'.PHP_EOL,
	'from' => 'e_subscriber',
	'condition' => array(
		array('e_subscriber.id = :id', array(':id'=>'id'), 'AND'),
	),
);

$nonce = uniqid(rand(0, 1000000));
if (!isset($_POST["nonce"])) {
	$_SESSION["nonce"] = \Routerunner\Crypt::crypter($nonce);
}

$value = array(
	"label" => "",
	"email" => "",
	"link" => "",
	"category" => "",
);
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
	$SQL = "SELECT label, email, link, category FROM `e_subscriber` WHERE id = ?";
	if ($result = \db::query($SQL, array($_GET["id"]))) {
		$value = array_merge($value, $result[0]);
	}
}

$input = array(
	'id' => array(
		'type' => 'hidden',
		'field' => 'id',
		'value' => (isset($_GET["id"]) ? $_GET["id"] : "")
	),
	'nonce' => array(
		'type' => 'hidden',
		'field' => 'nonce',
		'value' => $nonce
	),
	'date' => array(
		'type' => 'hidden',
		'field' => 'date',
		'value' => time()
	),
	'label' => array(
		'type' => 'text',
		'field' => 'label',
		'label' => 'Név',

		'input-id' => 'frm-name',
		'class' => '',

		'value' => $value["label"]
	),
	'email' => array(
		'type' => 'text',
		'field' => 'email',
		'label' => 'E-mail cím',

		'input-id' => 'frm-email',
		'class' => '',

		'value' => $value["email"]
	),
	'link' => array(
		'type' => 'text',
		'field' => 'link',
		'label' => 'Webcím',

		'input-id' => 'frm-link',
		'class' => '',

		'value' => $value["link"]
	),
	'category' => array(
		'type' => 'select',
		'field' => 'category',
		'label' => 'Kategória',

		'input-id' => 'frm-category',
		'class' => '',

		'value' => $value["category"]
	),
	'submit' => array(
		'type' => 'submit',
		'input-id' => 'frm-submit',
		'value' => 'Mentés'
	),
);