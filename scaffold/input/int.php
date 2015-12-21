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
?>

<div class="form-group form-md-line-input">
	<label for="property-<?=$field_name?>"><?=$field_name?></label>
	<input type="text" name="<?=$field_name?>" id="property-<?=$field_name?>" class="input form-control" value="<?=$value?>">
	<?=$help?>
</div>

<script>
	$("#property-<?=$field_name?>").TouchSpin({
		buttondown_class: 'btn green',
		buttonup_class: 'btn green',
		min: -1000000000,
		max: 1000000000,
		stepinterval: 50,
		maxboostedstep: 10000000,
		prefix: ' '
	});
</script>