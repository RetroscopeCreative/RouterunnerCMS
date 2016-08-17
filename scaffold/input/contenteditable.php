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
$help = '';
if (isset($field_data["help"]["panel"])) {
	$help = '<span class="help-block">' . $field_data["help"]["panel"] . '</span>';
}
?>
<div class="form-group form-md-line-input">
	<input type="text" name="<?=$field_name?>" id="property-<?=$field_name?>" class="input form-control" value="<?=$value?>">
	<label for="property-<?=$field_name?>"><?=$field_name?></label>
	<?=$help?>
</div>
