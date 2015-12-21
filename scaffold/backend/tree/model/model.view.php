<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 11:27
 */

$context = $runner->context;

$_model_context = array(
	"direct" => true,
	'self' => array(
		'reference' => $context['reference']
	),
	"session" => \runner::stack("session_id"),
);
$router = false;
$route = '/model/' . $context['model_class'];

\runner::redirect_route($route, \runner::config("scaffold"), true, $_model_context, $router, $model);
if (!$model && $runner->model) {
	$model = $runner->model;
}
$new_model = false;
if (!$model) {
	$model = new stdClass();
	foreach ($context as $attr => $value) {
		$model->$attr = $value;
	}
	$new_model = true;
}

$tree = \runner::stack('tree');
$route = false;
if ($branch = \Routerunner\Helper::tree_route($tree, $context['route'])) {
	if (isset($branch['children']['#' . $context['reference']])) {
		$route = '#' . $context['reference'];
	} elseif (isset($branch['children'][$context['model_class'] . '/' . $context['table_id']])) {
		$route = $context['model_class'] . '/' . $context['table_id'];
	} elseif (isset($branch['children'][$context['model_class']])) {
		$route = $context['model_class'];
	}
}

$traverse = \runner::stack('traverse');

if ($route) {
	$context['route'][] = $route;
	if ($node = \Routerunner\Helper::tree_route($tree, $context['route'])) {

		$parsed = \Routerunner\Helper::parse_array($node, $model, array("children"));

		$jstree = array(
			'type' => $context['model_class'],
		);
		if ($new_model) {
			$jstree['icon'] = 'fa fa-folder icon-state-warning';
		} elseif (isset($parsed['icon'])) {
			$jstree['icon'] = $parsed['icon'];
		}
		if (isset($traverse['current']['reference'])
			&& $traverse['current']['reference'] == $context['reference']) {
			$jstree['selected'] = true;
		}
		$classes = array();
		// todo: or children in memory
		if ($children = \Routerunner\Bootstrap::children($context['reference'])) {
			$classes[] = ($context["open"] ? 'jstree-open' : 'jstree-closed');
		}
		if ($context["open"]) {
			$classes[] = 'node-open';
		}

		$html = ' data-reference="' . $context['reference'] . '" data-table_id="' . $context['table_id'] . '" data-model_class="' . $context['model_class'] . '"';
		if ($classes) {
			$html .= ' class="' . implode(' ', $classes) . '"';
		}
		if (isset($parsed['li_attr']) && $parsed['li_attr']) {
			$html .= (is_array($parsed['li_attr']) ? implode(' ', $parsed['li_attr']) : $parsed['li_attr']);
		}

		if ($new_model) {
			$label = ucfirst($context['model_class']);
		} else {
			$label = (isset($context['label']) ? $context['label'] : $parsed['text']);
		}

		?>
		<li id="jstreenode_<?=$parsed['id']?>"<?=$html?> data-jstree='<?=json_encode($jstree)?>' data-route='<?=json_encode($context['route'])?>' data-model='<?=json_encode($model)?>'>
			<?=$label?>
			<?php
			\runner::route('/backend/tree/container', $context, $router);
			?>
		</li>
<?php
	}
}

