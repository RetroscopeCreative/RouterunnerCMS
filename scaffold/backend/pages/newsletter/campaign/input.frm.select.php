<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.07.13.
 * Time: 11:27
 */


$value = (input("value") ? input("value") : array());
if ($value) {
	$value = explode(",", $value);
}

$SQL = "SELECT category FROM `e_subscriber` WHERE category <> '' GROUP BY category ORDER BY category";
$result = \db::query($SQL);
?>
<div class="form-group">
	<label for="<?=input("input-id")?>"><?=input("label")?></label>
	<select placeholder="<?=input("label")?>" id="<?=input("input-id")?>" class="select2 form-control" multiple>
		<?php
		if ($result) {
			foreach ($result as $row) {
				$selected = ($value && in_array($row["category"], $value)) ? " selected" : "";
				echo '	<option value="' . $row["category"] . '" ' . $selected . '>' . $row["category"] . '</option>' . PHP_EOL;
			}
		}
		?>
	</select>
	<input type="hidden" name="<?=input("field")?>" id="select-<?=input("field")?>" value="<?=input("value")?>" />
</div>

<script>
	$(document).ready(function() {
		$("#<?=input("input-id")?>").select2({
			tags: true
		});
		$("#<?=input("input-id")?>").on("change", function() {
			$("#select-<?=input("field")?>").val($(this).val());
		});
	});
</script>