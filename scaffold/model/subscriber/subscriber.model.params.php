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

$from = 'e_subscriber';

//$orderBy = \Routerunner\Routerunner::BY_TREE;
if (isset($_GET["subscriber_search"])) {
	$where = array(
		"label LIKE :like OR link LIKE :like OR email LIKE :like OR category LIKE :like" => '%' . $_GET["subscriber_search"] . '%'
	);
} else {
	$where = array();
	$limit = 50;
}

$orderBy = 'date DESC';
$primary_key = 'id';
$force_list = true;
