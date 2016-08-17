<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 11:35
 */

/*
// model parameters
$model = "menu";
$from = "cs_menu";
$select = array("label");
$where = array("cs_menu_id > ?" => 1);
$orderBy = 'cs_menu_id DESC';
$limit = 5;
// SQL
$SQL = "SELECT label FROM cs_menu ORDER BY cs_menu_id";
*/

$from = 'e_campaign';
$leftJoin = array(
	'e_cron ON e_cron.campaign = e_campaign.id',
);
$select = array(
	'primary_key' => "CONCAT(CAST(e_campaign.id AS char(128)), '-', CAST(COALESCE(e_cron.id, 0) AS char(128)))",
	'id' => 'COALESCE(e_cron.id, 0)',
	'campaign_id' => 'e_campaign.id',
	'active' => 'e_campaign.active',
	'sent' => "COALESCE((SELECT COUNT(DISTINCT d.id) FROM e_delivered AS d WHERE d.cron_id = e_cron.id), 0)",
	'to_send' => "COALESCE((SELECT COUNT(DISTINCT s.email) FROM e_subscriber AS s WHERE s.unsubscribe IS NULL AND CONCAT(',',e_campaign.category,',') LIKE CONCAT('%,',s.category,',%')), 0) - COALESCE((SELECT COUNT(DISTINCT d.id) FROM e_delivered AS d WHERE d.cron_id = e_cron.id), 0)",
	'test_address' => "COALESCE(e_cron.test_address, '')",
	'limit_per_period' => 'COALESCE(e_cron.limit_per_period, 100)',
	'period' => 'COALESCE(e_cron.period, 3600)',
	'start' => 'e_cron.start',
	'finish' => 'e_cron.finish',
);

$SQL = "SELECT id FROM e_cron WHERE start IS NOT NULL AND finish IS NULL";
\runner::now("e_cron_running", false);

//$orderBy = \Routerunner\Routerunner::BY_TREE;
if (isset($_GET["campaign_search"])) {
	$where = array(
		//"active = '1'" => null,
		"label LIKE :like OR category LIKE :like OR mail_html LIKE :like" => '%' . $_GET["campaign_search"] . '%'
	);
} elseif ($result = \db::query($SQL)) {
	$where = array("e_cron.id = :id" => $result[0]["id"]);
	\runner::now("e_cron_running", $result[0]["id"]);
} else {
	$where = array(
		"active = '1'" => null,
		/*
		"start IS NULL" => null,
		"finish IS NULL" => null,
		*/
	);
	$limit = 50;
}

$orderBy = 'COALESCE(e_cron.id, 0) DESC, e_campaign.id DESC';
$primary_key = 'primary_key';
$force_list = true;
