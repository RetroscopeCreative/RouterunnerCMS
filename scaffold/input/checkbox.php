<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.12.04.
 * Time: 21:33
 */


$debug = 1;
$help = '';
if (isset($field_data["help"]["panel"])) {
	$help = '<span class="help-block">' . $field_data["help"]["panel"] . '</span>';
}
$checked = '';
if (isset($value) && ($value === "1" || $value === true || $value === "true" || $value === "on")) {
	$checked = ' checked';
} elseif (isset($value) && ($value === "0" || $value === false || $value === "false" || $value === "off")) {
	$checked = '';
} elseif (isset($field_data["default"]) && filter_var($field_data["default"], FILTER_VALIDATE_BOOLEAN)) {
	$checked = ' checked';
}
?>

<div class="form-group form-md-line-input">
	<div class="col-md-offset-2 col-md-4">
		<div class="md-checkbox-list">
			<div class="md-checkbox">
				<input type="checkbox" id="property-<?=$field_name?>" name="<?=$field_name?>"<?=$checked?> class="md-check">
				<label for="property-<?=$field_name?>">
					<span></span>
					<span class="check"></span>
					<span class="box"></span>
					<?=$field_name?></label>
			</div>
		</div>
	</div>
</div>
