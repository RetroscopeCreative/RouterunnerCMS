<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.10.23.
 * Time: 16:23
 */

$runner->backend_context['parents'] = \Routerunner\Bootstrap::parent($runner->context['reference']);
$current = $runner->context['reference'];
$runner->backend_context['siblings'] = \Routerunner\Bootstrap::siblings($runner->context['reference'], false, $current);
$runner->backend_context['current'] = $runner->backend_context['siblings'][$current];

$parents_ref = array();
foreach ($runner->backend_context['parents'] as $parent) {
	$parents_ref[] = $parent['reference'];
}
$runner->backend_context['parents_ref'] = $parents_ref;

\runner::stack('traverse', $runner->backend_context);