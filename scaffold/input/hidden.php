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
if (empty($field_name)) {
	$field_name = (isset($fieldname) ? $fieldname : uniqid());
}
?>
<input type="hidden" name="<?=$field_name?>" id="property-<?=$field_name?>" value="<?=$value?>" />
