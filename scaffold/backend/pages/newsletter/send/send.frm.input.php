<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.07.13.
 * Time: 11:27
 */

$debug = 1;

$method = 'post';
if (isset($_GET["id"])) {
	$SQL = "SELECT id FROM e_cron WHERE start IS NOT NULL AND finish IS NULL AND id = :id";
	if (\db::query($SQL, array(":id" => $_GET["id"]))) {
		$method = 'put';
	}
}

$form = array(
	'method' => 'post',
	'xmethod' => $method,
	'name' => 'e_cron',
	'error_format' => '<p class="err">%s</p>'.PHP_EOL,
	'from' => 'e_cron',
	'condition' => array(
		array('e_cron.id = :id', array(':id'=>'id'), 'AND'),
	),
);

$nonce = uniqid(rand(0, 1000000));
if (!isset($_POST["nonce"])) {
	$_SESSION["nonce"] = \Routerunner\Crypt::crypter($nonce);
}

$value = array(
	"campaign" => "",
	"test_address" => "",
	"limit_per_period" => 100,
	"period" => 3600,
	"start" => "",
	"finish" => "",
);
if (isset($_GET["id"]) && is_numeric($_GET["id"]) && $_GET["id"] > 0) {
	$SQL = "SELECT campaign, test_address, limit_per_period, period, start, finish FROM `e_cron` WHERE id = ?";
	if ($result = \db::query($SQL, array($_GET["id"]))) {
		$value = array_merge($value, $result[0]);
	}
}
if (isset($_GET["cid"]) && is_numeric($_GET["cid"])) {
	$SQL = "SELECT id, label, category, active FROM `e_campaign` WHERE id = ?";
	if ($result = \db::query($SQL, array($_GET["cid"]))) {
		$campaign_data = $result[0];
	}

}


$input = array(
	'id' => array(
		'type' => 'hidden',
		'field' => 'id',
		'value' => (isset($_GET["id"]) ? $_GET["id"] : "")
	),
	'campaign' => array(
		'type' => 'hidden',
		'field' => 'campaign',
		'value' => $campaign_data["id"]
	),
	'nonce' => array(
		'type' => 'hidden',
		'field' => 'nonce',
		'value' => $nonce
	),
	'label' => array(
		'type' => 'label',
		'field' => 'label',
		'label' => 'Campaign label',

		'input-id' => 'frm-label',
		'class' => '',

		'value' => $campaign_data["label"]
	),
	'category' => array(
		'type' => 'label',
		'field' => 'category',
		'label' => 'Campaign category',

		'input-id' => 'frm-category',
		'class' => '',

		'value' => $campaign_data["category"]
	),
	'active' => array(
		'type' => 'label',
		'field' => 'active',
		'label' => 'Is active?',

		'input-id' => 'frm-active',
		'class' => '',

		'value' => ($campaign_data["active"] == "1" ? "yes" : "no"),
	),
	'start' => array(
		'type' => 'label',
		'field' => 'start',
		'label' => 'Job started',

		'input-id' => 'frm-start',
		'class' => '',

		'value' => $value["start"]
	),
	'finish' => array(
		'type' => 'label',
		'field' => 'finish',
		'label' => 'Job finished',

		'input-id' => 'frm-finish',
		'class' => '',

		'value' => $value["finish"]
	),
);

$unset = array(
	"label",
	"category",
	"active",
	"start",
	"submit"
);

if ($value["start"] && !$value["finish"]) {
	$input["finish"]["value"] = time();
	$input["submit"] = array(
		'type' => 'submit',
		'input-id' => 'frm-submit',
		'class-addon' => 'btn-danger',
		'value' => 'Stop immediately'
	);
} else {
	$unset[] = "id";
	$unset[] = "finish";
	$input["test_address"] = array(
		'type' => 'text',
		'field' => 'test_address',
		'label' => 'Test address(es)',

		'input-id' => 'frm-test_address',
		'class' => '',

		'value' => $value["test_address"]
	);
	$input["limit_per_period"] = array(
		'type' => 'text',
		'field' => 'limit_per_period',
		'label' => 'Limit per period',

		'input-id' => 'frm-limit_per_period',
		'class' => '',

		'value' => $value["limit_per_period"]
	);
	$input["period"] = array(
		'type' => 'text',
		'field' => 'period',
		'label' => 'Period (sec)',

		'input-id' => 'frm-period',
		'class' => '',

		'value' => $value["period"]
	);
	$input["submit"] = array(
		'type' => 'submit',
		'input-id' => 'frm-submit',
		'class-addon' => 'btn-primary',
		'value' => 'Send newsletters'
	);
}
