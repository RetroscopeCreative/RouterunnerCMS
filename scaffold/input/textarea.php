<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.12.04.
 * Time: 21:33
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
$row = ' rows="4"';
if (isset($field_data["row"]) && is_numeric($field_data["row"])) {
    $row = ' rows="' . $field_data["row"] . '"';
}
?>
<div class="form-group form-md-line-input">
	<label for="property-<?=$field_name?>"><?=$field_name?></label>
	<textarea name="<?=$field_name?>" id="property-<?=$field_name?>" class="input form-control"<?=$row?>><?=$value?></textarea>
	<?=$help?>
</div>
