<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.07.13.
 * Time: 11:27
 */


$value = (input("value") ? input("value") : false);
if ($value) {
	$value = explode(',', $value);
}
$options = json_decode(input("options"));
?>
<div class="form-group">
	<label for="<?=input("input-id")?>"><?=input("label")?></label>
	<select placeholder="<?=input("label")?>" id="<?=input("input-id")?>" class="select2 form-control" multiple="multiple">
		<?php
		if ($options) {
			foreach ($options as $option => $label) {
				$selected = ($value && in_array($option, $value)) ? " selected" : "";
				echo '	<option value="' . $option . '" ' . $selected . '>' . $label . '</option>' . PHP_EOL;
			}
		}
		?>
	</select>
	<input type="hidden" name="<?=input("field")?>" id="select-<?=input("field")?>" value="<?=input("value")?>" />
</div>

<script>
	$(document).ready(function() {
		$("#<?=input("input-id")?>").select2({
		});
		$("#<?=input("input-id")?>").on("change", function() {
			$("#select-<?=input("field")?>").val($(this).val());
		});
	});
</script>