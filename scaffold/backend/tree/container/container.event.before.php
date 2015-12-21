<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.10.23.
 * Time: 16:23
 */


$context = $runner->context;

$tree = \runner::stack('tree');
$branch = \Routerunner\Helper::tree_route($tree, $context['route']);

$accept = array();
if ($branch = \Routerunner\Helper::tree_route($tree, $context['route'])) {
	$accept = $branch;
}
$runner->context['accept'] = $accept;

if ($runner->model) {
	$runner->model->accept = $accept;
}

$debug = 1;
