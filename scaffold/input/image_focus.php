<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.10.23.
 * Time: 20:05
 */
$debug = 1;
$value = (isset($field_data["default"]) ? $field_data["default"] : "");
if (isset($runner->context["model"]) && is_array($runner->context["model"])
	&&  isset($runner->context["model"][0], $runner->context["model"][0]->$field_name)) {
	$value = $runner->context["model"][0]->$field_name;
} elseif (isset($runner->context["model"]) && is_object($runner->context["model"])
	&& isset($runner->context["model"]->$field_name)) {
	$value = $runner->context["model"]->$field_name;
}

if ($value && ($json = json_decode($value, true)) && isset($json["src"])) {
	$src = $json["src"];
} elseif ($value) {
	$src = $value;
} else {
	$src = (isset($field_data["default"]) ? $field_data["default"] : "/" . \runner::config("BACKEND_DIR") . "/placeholder/100x100");
}
?>
<div class="form-group form-md-line-input" style="width: 100%;">
	<div class="input-group form-control" style="width: 100%;">
		<label for="property-<?=$field_name?>" class="col-md-3">Choose File</label>
		<input type="text" name="<?=$field_name?>" id="property-<?=$field_name?>" class="col-md-9" />
		<div id="preview-<?=$field_name?>" class="preview" data-src="<?php echo $src; ?>" style="width: 100%; height: 300px; background-image: url(<?php echo $src; ?>); background-size: cover; background-position: 50% 50%;">
		</div>
	</div>
</div>
