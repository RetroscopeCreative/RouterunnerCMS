<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.10.23.
 * Time: 20:05
 */
$value = (isset($field_data["default"]) ? $field_data["default"] : "");
if (isset($runner->context["model"]) && is_array($runner->context["model"])
	&&  isset($runner->context["model"][0], $runner->context["model"][0]->$field_name)) {
	$value = $runner->context["model"][0]->$field_name;
} elseif (isset($runner->context["model"]) && is_object($runner->context["model"])
	&& isset($runner->context["model"]->$field_name)) {
	$value = $runner->context["model"]->$field_name;
}
$strvalue = "";
if ($value && is_numeric($value)) {
	$strvalue = strftime("%Y-%m-%d %H:%M", $value);
} elseif ($value && ($timestamp = strtotime($value))) {
	$strvalue = strftime("%Y-%m-%d %H:%M", $timestamp);
}
$help = '';
if (isset($field_data["help"]["panel"])) {
	$help = '<span class="help-block">' . $field_data["help"]["panel"] . '</span>';
}
$debug = 1;
?>
<div class="form-group form-md-line-input">
	<div class="input-icon right">
		<input type="text" id="property-<?=$field_name?>" name="<?=$field_name?>"  class="form-control" value="<?=$strvalue?>">
		<label for="property-<?=$field_name?>"><?=$field_name?></label>
		<?=$help?>
		<i class="fa fa-calendar"></i>
	</div>
</div>

<script>
	$("#property-<?=$field_name?>").bootstrapMaterialDatePicker({
		format: 'YYYY-MM-DD HH:mm',
		lang: 'hu',
		weekStart: 1,
		okButton: "OK",
		cancelButton: "Cancel"
	});
	$("#property-<?=$field_name?>").change(function() {
		var val = $(this).val();
		if (val) {
			$("#property-<?=$field_name?>").bootstrapMaterialDatePicker("setDate", val);
		}
	});
	$("#property-<?=$field_name?>").trigger("change");
</script>