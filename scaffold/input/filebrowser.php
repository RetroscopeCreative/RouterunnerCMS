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

if (!$value) {
	$value = "data:image/gif;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO9TXL0Y4OHwAAAABJRU5ErkJggg==";
}
?>
<div class="form-group form-md-line-input" style="width: 100%;">
	<div class="input-group form-control">
		<input type="hidden" name="<?=$field_name?>" value='<?=$value?>' />
		<img id="property-<?=$field_name?>" class="input img-input" src="<?=$value?>" />
	</div>
	<label for="property-<?=$field_name?>"><?=$field_name?></label>
</div>
