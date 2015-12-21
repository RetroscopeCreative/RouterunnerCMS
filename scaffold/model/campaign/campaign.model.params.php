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
$select = array(
	'sent' => "COALESCE((SELECT COUNT(DISTINCT d.id) FROM e_delivered AS d WHERE d.cron_id IN (SELECT id FROM e_cron AS c WHERE c.campaign = e_campaign.id)), 0)",
	'to_send' => "COALESCE((SELECT COUNT(DISTINCT s.email) FROM e_subscriber AS s WHERE s.unsubscribe IS NULL AND CONCAT(',',e_campaign.category,',') LIKE CONCAT('%,',s.category,',%')), 0) - COALESCE((SELECT COUNT(DISTINCT d.id) FROM e_delivered AS d WHERE d.cron_id IN (SELECT id FROM e_cron AS c WHERE c.campaign = e_campaign.id)), 0)",
	'opened' => "COALESCE((SELECT COUNT(DISTINCT d.id) FROM e_stat AS s LEFT JOIN e_delivered AS d ON d.id = s.deliver_id WHERE d.cron_id IN (SELECT id FROM e_cron AS c WHERE c.campaign = e_campaign.id) AND s.method = 'open'), 0)",
	'clicked' => "COALESCE((SELECT COUNT(DISTINCT d.id) FROM e_stat AS s LEFT JOIN e_delivered AS d ON d.id = s.deliver_id WHERE d.cron_id IN (SELECT id FROM e_cron AS c WHERE c.campaign = e_campaign.id) AND s.method = 'click'), 0)",
);

//$orderBy = \Routerunner\Routerunner::BY_TREE;
if (isset($_GET["campaign_search"])) {
	$where = array(
		"label LIKE :like OR category LIKE :like OR mail_html LIKE :like" => '%' . $_GET["campaign_search"] . '%'
	);
} else {
	$where = array();
	$limit = 50;
}

$orderBy = 'id DESC';
$primary_key = 'id';
$force_list = true;
