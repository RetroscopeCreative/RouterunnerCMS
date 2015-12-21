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

$from = 'member';

$select = array(
	'id' => 'member.id',
	'email' => 'member.email',
	'reg_date' => 'FROM_UNIXTIME(member.reg_date)',
	'confirm_date' => 'FROM_UNIXTIME(member.confirm_date)',
	'last_login' => 'FROM_UNIXTIME(member.last_login)',
	'last_ip' => 'member.last_ip',
	'licence' => "member.licence",

	'usergroup_id' => "ugroup.usergroup_id",
	'usergroup_label' => "ugroup.label",
);

$leftJoin = array(
	'{PREFIX}user AS u ON u.email = member.email',
	'{PREFIX}usergroup AS ugroup ON ugroup.usergroup_id = u.usergroup',
);

//$orderBy = \Routerunner\Routerunner::BY_TREE;
if (isset($_GET["search"])) {
	$where = array(
		"member.email LIKE :like OR ugroup.label LIKE :like OR member.last_ip LIKE :like" => '%' . $_GET["search"] . '%'
	);
} else {
	$where = array();
	$limit = 50;
}

$orderBy = 'member.reg_date DESC';
$primary_key = 'id';
$force_list = true;

//\runner::now("debug::model->load", true);
