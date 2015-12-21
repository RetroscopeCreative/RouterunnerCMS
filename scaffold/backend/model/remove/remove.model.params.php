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

/*
$from = \runner::config('PREFIX') . 'models';
$orderBy = \Routerunner\Routerunner::BY_TREE;
$where = array(
	'parent' => array('reference' => $runner->context['reference']),
);
*/
$SQL = <<<SQL
SELECT models.reference, models.model_class, models.table_id
FROM `{PREFIX}models` AS models
 LEFT JOIN `{PREFIX}model_trees` AS trees ON trees.reference = models.reference
WHERE trees.parent_ref = :reference
SQL;
$SQLhash = \Routerunner\Crypt::crypter($SQL, null, null, 0, 'SQLchecker');
$where = array(
	':reference' => $runner->context['reference']
);

$primary_key = 'reference';
$force_list = true;
