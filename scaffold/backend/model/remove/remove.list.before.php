<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 11:27
 */
$debug = 1;
$allowed = true;
if (\runner::stack("models_created") &&
	isset($runner->context["reference"], \runner::stack("models_created")[$runner->context["reference"]])) {
	$model_data = \runner::stack("models_created")[$runner->context["reference"]];

	$model = new \Routerunner\BaseModel($model_data['route'], $model_data);
	$model->permission = $model_data['permission'];

	if ($model && is_object($model) && $model->permission && !$model->deletable()) {
		$allowed = false;
	} elseif ($model && !is_object($model)) {
		$allowed = false;
	}
	if ($allowed) {
		$context = array(
			"direct" => $runner->context["reference"],
			"session" => \runner::stack("session_id"),
			"silent" => true,
		);

		$model_route = "/model/" . $runner->context["model_class"];
		\runner::redirect_route($model_route, \runner::config("scaffold"), true, $context, $router, $model);
		if (is_array($model)) {
			$model = array_shift($model);
		}

		$runner->context["model"] = $model;
	}
} elseif (isset($runner->context["route"], $runner->context["reference"])) {
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
	if ($model && $model->permission && !$model->deletable()) {
		$allowed = false;
	}
}
$runner->context["allowed"] = $allowed;
?>

<div class="portlet light">
	<div class="portlet-title">
		<div class="caption font-red col-md-6">
			<i class="fa fa-trash-o font-red"></i>
			<span class="caption-subject bold uppercase"> Remove</span>
		</div>
		<?php
		if ($runner->context["allowed"]) {
			?>
			<div class="col-md-6">
				<div class="pull-right">
					<input type="checkbox" id="are_you_sure" class="make-switch"
						   data-on-text="&nbsp;YES!<br>Really sure!&nbsp;" data-off-text="&nbsp;Are you sure?&nbsp;"
						   data-on-color="danger" data-off-color="info" value="1"/>
				</div>
			</div>
		<?php
		}
		?>
	</div>
		<?php
		if ($runner->context["allowed"]) {
		?>
	<div class="portlet-body form" style="display: none;">
		<h3 class="text-warning">Are you sure to remove this model and its decendants?</h3>
		<label>The following models (and their descendant) will be removed:</label>
		<ul>
			<?php
			} else {
			?>
	<div class="portlet-body form">
		<h3 class="text-danger">Remove not allowed for this model!</h3>
		<?php
		}
