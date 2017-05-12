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
	'reference' => \runner::config('PREFIX') . 'models.reference',
	'model_class' => \runner::config('PREFIX') . 'models.model_class',
	'table_id' => \runner::config('PREFIX') . 'models.table_id',
	'accept' => 'NULL',
);

$from = \runner::config('PREFIX') . 'models';

$leftJoin = array(
	\runner::config('PREFIX') . 'model_trees AS trees ON trees.reference = ' . \runner::config('PREFIX') . 'models.reference'
);

$where = array();
if (!empty($runner->context['reference'])) {
	$where['{PREFIX}models.reference = :reference'] = $runner->context['reference'];
}
if (!empty($runner->context['model_class'])) {
	$where['{PREFIX}models.model_class = :modelclass'] = $runner->context['model_class'];
}
if (!empty($runner->context['table_id'])) {
	$where['{PREFIX}models.table_id = :tableid'] = $runner->context['table_id'];
}
$primary_key = 'id';
$force_view = true;
$skip_referencing = true;
