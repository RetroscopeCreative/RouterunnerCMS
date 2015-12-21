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

$from = 'e_stat';
$leftJoin = array(
	'e_delivered ON e_delivered.id = e_stat.deliver_id',
	'e_subscriber ON e_subscriber.id = e_delivered.address_id',
	'e_cron ON e_cron.id = e_delivered.cron_id',
	'e_campaign ON e_campaign.id = e_cron.campaign',
);
$select = array(
	'stat_id' => 'e_stat.id',
	'activity_date' => 'e_stat.date',
	'activity' => 'e_stat.method',
	'clicked' => 'e_stat.click',
	'name' => 'e_subscriber.label',
	'email' => 'e_subscriber.email',
	'category' => 'e_subscriber.category',
	'unsubscribe_date' => 'e_subscriber.unsubscribe',
	'send_date' => 'e_delivered.date',
	'campaign_label' => 'e_campaign.label',
	'subject' => 'e_campaign.subject',
);

//$orderBy = \Routerunner\Routerunner::BY_TREE;
if (isset($_GET["search"])) {
	$where = array(
		//"active = '1'" => null,
		"e_subscriber.label LIKE :like OR e_subscriber.email LIKE :like OR e_subscriber.category LIKE :like " .
			" OR e_campaign.label LIKE :like OR e_campaign.subject LIKE :like" .
			" OR e_stat.method LIKE :like OR e_stat.click LIKE :like" =>
			'%' . $_GET["search"] . '%'
	);
} else {
	$where = array(
		"e_campaign.active = '1'" => null,
	);
	$limit = (isset($_GET["limit"]) ? $_GET["limit"] : 50);
}

$orderBy = 'e_stat.date DESC';
$primary_key = 'stat_id';
$force_list = true;
