<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 11:27
 */
?>
<div class="portlet light">
	<div class="portlet-title">
		<div class="caption font-red col-md-12">
			<i class="fa fa-arrows font-red"></i>
			<span class="caption-subject bold uppercase"> Movement</span>
		</div>
	</div>
	<div class="portlet-body form">

<?php
$debug = 1;

$allowed = true;
if (isset($runner->context["route"], $runner->context["reference"])) {
	$context = array(
		"direct" => $runner->context["reference"],
		"silent" => true
	);
	$model = \model::load($context, $runner->context["route"], $router, false, \runner::config("scaffold"));
	if (is_array($model)) {
		$models = $model;
		foreach ($models as $item) {
			if ($item->reference == $runner->context["reference"]) {
				$model = $item;
				break;
			}
		}
	}
	if ($model && is_object($model) && $model->permission && !$model->movable()) {
		$allowed = false;
	} elseif ($model && !is_object($model)) {
		$allowed = false;
	}
}
if ($allowed) {
	$traverse = $runner->backend_context;

	$scaffold = \Routerunner\Helper::$scaffold_root;
	$tree = (@include $scaffold . '/model/tree.php');

	\runner::stack('traverse', $traverse, true);
	\runner::stack('tree', $tree, true);
	?>
	<form name="routerunner-movement" id="routerunner-movement" class="row" role="form"
		  data-jstreetypes='<?= json_encode($jstree_types) ?>'>
		<div id="routerunner-tree" data-traverse='<?= json_encode($traverse) ?>' class="col-xs-12">
		</div>
	</form>
<?php
} else {
	echo '	<h3 class="text-danger">Movement not allowed for this model!</h3>';
}
		?>
	</div>
</div>