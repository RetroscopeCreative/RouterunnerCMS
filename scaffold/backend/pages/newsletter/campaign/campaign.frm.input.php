<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.07.13.
 * Time: 11:27
 */

$debug = 1;
$form = array(
	'method' => 'post',
	'xmethod' => ((!empty($_GET["id"]) && is_numeric($_GET["id"]) && $_GET["id"] > 0) ? 'put' : 'post'),
	'name' => 'e_campaign',
	'error_format' => '<p class="err">%s</p>'.PHP_EOL,
	'from' => 'e_campaign',
	'condition' => array(
		array('e_campaign.id = :id', array(':id'=>'id'), 'AND'),
	),
);
/*
$nonce = uniqid(rand(0, 1000000));
if (!isset($_POST["nonce"])) {
	$_SESSION["nonce"] = \Routerunner\Crypt::crypter($nonce);
}
*/

$value = array(
	"label" => "",
	"category" => "",
	"active" => "1",
	"mail_route" => "",
	"subject" => "",
	"mail_html" => "",
);
if (!empty($_GET["id"]) && is_numeric($_GET["id"])) {
	$SQL = "SELECT label, category, active, mail_route, subject, mail_html, mail_text FROM `e_campaign` WHERE id = ?";
	if ($result = \db::query($SQL, array($_GET["id"]))) {
		$value = array_merge($value, $result[0]);
	}
}
if (!$value["mail_html"] && \runner::config("newsletter_mail_html")) {
	$value["mail_html"] = \runner::config("newsletter_mail_html");
}

$input = array(
	'id' => array(
		'type' => 'hidden',
		'field' => 'id',
		'value' => (!empty($_GET["id"]) ? $_GET["id"] : "")
	),
	/*
	'nonce' => array(
		'type' => 'hidden',
		'field' => 'nonce',
		'value' => $nonce
	),
	*/
	'label' => array(
		'type' => 'text',
		'field' => 'label',
		'label' => 'Label',

		'input-id' => 'frm-name',
		'class' => '',

		'value' => $value["label"]
	),
	'category' => array(
		'type' => 'select',
		'field' => 'category',
		'label' => 'Category',

		'input-id' => 'frm-category',
		'class' => '',

		'value' => $value["category"]
	),
	'active' => array(
		'type' => 'boolean',
		'field' => 'active',
		'label' => 'Active state',

		'input-id' => 'frm-active',
		'class' => '',

		'value' => $value["active"]
	),
	/*
	'mail_route' => array(
		'type' => 'text',
		'field' => 'mail_route',
		'label' => 'Levél útvonal',

		'input-id' => 'frm-mail_route',
		'class' => '',

		'value' => $value["mail_route"]
	),
	*/
	'subject' => array(
		'type' => 'text',
		'field' => 'subject',
		'label' => 'Subject',

		'input-id' => 'frm-subject',
		'class' => '',

		'value' => $value["subject"]
	),
	'mail_html' => array(
		'type' => 'ckeditor',
		'field' => 'mail_html',
		'label' => 'Message content',

		'input-id' => 'frm-mail_html',
		'class' => '',

		'value' => $value["mail_html"]
	),
	'mail_text' => array(
		'type' => 'textarea',
		'field' => 'mail_text',
		'label' => 'Message text content',

		'input-id' => 'frm-mail_text',
		'class' => '',

		'value' => $value["mail_text"]
	),
	'submit' => array(
		'type' => 'submit',
		'input-id' => 'frm-submit',
		'value' => 'Save'
	),
);

$unset = array(
	"submit"
);
if (empty($_GET["id"])) {
	$unset[] = 'id';
}