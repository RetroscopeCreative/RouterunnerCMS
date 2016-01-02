<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 11:27
 */

$allowed = true;
if (isset($runner->context["reference"], $runner->context["model_class"])) {
	$router = false;
	$model = false;
	/*
	$context = array(
		"self" => array("reference" => $runner->context["reference"])
	);
	*/
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

	if ($model && $model->permission && !$model->writable()) {
		$allowed = false;
	}
}

?>
<div class="portlet light">
	<div class="portlet-title">
		<div class="caption font-red col-md-6">
			<i class="fa fa-file-text-o font-red"></i>
			<span class="caption-subject bold uppercase"> Properties</span>
		</div>
		<div class="col-md-6">


<?php
$form_content = '';
if ($allowed) {
	$filters = array("hidden-on-page", "visible-on-page");
	$hiddens = array();

	$fields = (isset($router->runner->backend_context["model"]["fields"])
		? $router->runner->backend_context["model"]["fields"] : array());
	$route = (isset($runner->context["route"]) ? explode("/", $runner->context["route"]) : false);
	$scaffold = \Routerunner\Helper::$scaffold_root . DIRECTORY_SEPARATOR . "input";
	$backend_scaffold = \Routerunner\Helper::$document_root . DIRECTORY_SEPARATOR . \runner::config("BACKEND_DIR") . DIRECTORY_SEPARATOR . 'scaffold' . DIRECTORY_SEPARATOR . "input";

	foreach ($fields as $field_name => $field_data) {
		$_route = $route;
		$input = false;
		while (!$input && count($_route)) {
			if (file_exists($scaffold . implode(DIRECTORY_SEPARATOR, $_route) . DIRECTORY_SEPARATOR . $field_name . ".php")) {
				$input = $scaffold . implode(DIRECTORY_SEPARATOR, $_route) . DIRECTORY_SEPARATOR . $field_name . ".php";
			} elseif (file_exists($scaffold . implode(DIRECTORY_SEPARATOR, $_route) . DIRECTORY_SEPARATOR
				. $field_data["type"] . ".php")) {
				$input = $scaffold . implode(DIRECTORY_SEPARATOR, $_route) . DIRECTORY_SEPARATOR . $field_data["type"] . ".php";
			}
			if (!$input && count($_route)) {
				array_pop($_route);
			}
		}
		if (!$input) {
			if (file_exists($backend_scaffold . DIRECTORY_SEPARATOR . $field_name . ".php")) {
				$input = $backend_scaffold . DIRECTORY_SEPARATOR . $field_name . ".php";
			} elseif (file_exists($backend_scaffold . DIRECTORY_SEPARATOR . $field_data["type"] . ".php")) {
				$input = $backend_scaffold . DIRECTORY_SEPARATOR . $field_data["type"] . ".php";
			}
		}
		if ($input) {
			if (!in_array($field_data["type"], $filters)) {
				$filters[] = $field_data["type"];
			}

			$field_json = str_replace(array("\n", "'"), array("", '"'), json_encode($field_data, JSON_HEX_APOS));
			$value = "";
			if (isset($model->$field_name)) {
				$value = $model->$field_name;
			}
			$value = str_replace(array("\n", "'"), array("", '"'), $value);

			$reference = 'reference_' . time();
			if (isset($model->reference)) {
				$reference = 'reference_' . $model->reference;
			}
			$form_content .= <<<HTML
	<div id='panel-property-{$field_name}' class='panel-property {$field_data["type"]}' data-routerunner-id='panel-property-{$reference}-{$field_name}' data-field-name='{$field_name}' data-field-data='{$field_json}' data-{$field_name}='{$value}'>

HTML;

			ob_start();
			include $input;
			$form_content .= ob_get_clean();
			$form_content .= '</div>' . PHP_EOL;
		}
	}
	if ($filters) {
		$options = '';
		while ($filter = array_shift($filters)) {
			//$selected = ($filter == "hidden-on-page" ? ' selected' : '');
			$selected = '';
			$options .= '<option value="' . $filter . '"' . $selected . '>' . $filter . '</option>';
		}

		echo <<<HTML
		<div class="form-group form-md-line-input">
			<select class="form-control" id="property_filter" size="1" multiple>
				{$options}
			</select>
			<label for="property_filter">Filter</label>
		</div>
HTML;
	}
} else {
	$form_content = '<h3 class="text-danger">Properties not allowed for this model!</h3>';
}
?>


		</div>
	</div>
	<div class="portlet-body form">
		<form name="routerunner-properties" id="routerunner-properties" role="form">
			<div class="form-body">


<?php
echo $form_content;
?>
			</div>
		</form>
	</div>
</div>
<script>
	routerunner.page.current_model.panel.instance("properties").filter_init();
</script>
