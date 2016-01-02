<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 11:27
 */

$accept = array();
if ($model = $runner->model) {
	$accept = model::property('accept');
} elseif (isset($runner->context['accept'])) {
	$accept = $runner->context['accept'];
}
if (!function_exists('reorder')) {
	function reorder($children)
	{
		$order = array(0);
		array_walk($children, function ($val) use (&$order) {
			$order[] = $val['reference'];
		});
		return $order;
	}
}

$element = (isset($accept['panel']['element']) ? $accept['panel']['element'] : 'ul');

$html = '';

$parsed = \Routerunner\Helper::parse_array($accept, $model, array("children"));
if (isset($parsed['panel']['attr']) && is_array($parsed['panel']['attr'])) {
	foreach ($parsed['panel']['attr'] as $attr => $value) {
		$html .= ' ' . $attr . '="' . $value . '"';
	}
} elseif (isset($parsed['panel']['attr']) && is_string($parsed['panel']['attr'])) {
	$html .= ' ' . $parsed['panel']['attr'];
}
$open = false;
$select = false;

$traverse = \runner::stack('traverse');

$models_created = \runner::stack("models_created");

$open = (isset($runner->context['open']) ? $runner->context['open'] : false);

$debug = 1;
?>
	<<?=$element.$html?>>
	<?php
	$router = false;
	if ($open) {
		$removed = array();
		$children = \Routerunner\Bootstrap::children($runner->context['reference']);

		foreach ($children as $index => $child) {
			$open = (in_array($child['reference'], $traverse['parents_ref']) ? true : false);
			if ($models_created) {
				foreach ($models_created as $created_data) {
					if (in_array($child['reference'], $created_data["parent"])) {
						$open = true;
					}
				}
			}
			$override = null;

			$child_context = array(
				'reference' => $child['reference'],
				//'lang' => \runner::config("language"),
				'model_class' => $child['model_class'],
				'table_id' => $child['table_id'],
				'route' => $runner->context['route'],
				'open' => $open,
			);
			if (isset($child['label'])) {
				$override = array('label' => $child['label']);
				//$child_context['label'] = $child['label'];
			}

			$root = \runner::config("BACKEND_DIR") . DIRECTORY_SEPARATOR . 'scaffold';
			\runner::route('/backend/tree/model', $child_context, $router, false, $override, $root);
		}
	}

?>
	</<?=$element?>>

