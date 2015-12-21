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

$from = \runner::config('PREFIX') . 'changes AS changes';
$select = array(
	'user_email' => 'user.email',
	'user_name' => 'user.name',
	'user_group' => 'user.usergroup',
);
$leftJoin = array(
	'`{PREFIX}sessions` AS sessions ON sessions.session_id = changes.session',
	'`{PREFIX}user` AS user ON user.user_id = sessions.user'
);
$orderBy = "change_id DESC";

$where = array();
if (isset($runner->context['reference'])) {
	$where['changes.reference = :reference'] = $runner->context['reference'];
}
if (isset($runner->context['user'])) {
	$where['sessions.user = :user'] = $runner->context['user'];
}
if (isset($runner->context['session'])) {
	$where['changes.session = :session'] = $runner->context['session'];
}
$primary_key = 'change_id';
$force_view = true;