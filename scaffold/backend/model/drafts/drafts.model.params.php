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

$from = \runner::config('PREFIX') . 'drafts';
$orderBy = "date DESC";

$where = array();
if (isset($runner->context['reference'])) {
	$where['reference = :reference'] = $runner->context['reference'];
}
if (isset($runner->context['user'])) {
	$where['user = :user'] = $runner->context['user'];
}
$primary_key = 'id';
$force_list = true;
