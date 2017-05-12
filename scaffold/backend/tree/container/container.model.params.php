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

$select = array(
	'id' => 'trees.model_tree_id',
	'lang' => 'trees.lang',
	'parent' => 'trees.parent_ref',
	'reference' => 'models.reference',
	'model_class' => 'models.model_class',
	'table_id' => 'models.table_id',
	'accept' => 'NULL',
	'open' => 'NULL',
);

$from = \runner::config('PREFIX') . 'models AS models';

$leftJoin = array(
	'{PREFIX}model_trees AS trees ON trees.reference = models.reference'
);

$where = array();
if (!empty($runner->context['reference'])) {
	$where['models.reference = :reference'] = $runner->context['reference'];
}
if (!empty($runner->context['model_class'])) {
	$where['models.model_class = :modelclass'] = $runner->context['model_class'];
}
if (!empty($runner->context['table_id'])) {
	$where['models.table_id = :tableid'] = $runner->context['table_id'];
}
if (empty($where)) {
	$where['1 = 0'] = null;
}
$primary_key = 'id';
$force_view = true;
$skip_referencing = true;
