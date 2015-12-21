<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.07.13.
 * Time: 11:27
 */


$value = (input("value") ? input("value") : false);
if (!$value) {
	$SQL = "SELECT category FROM `e_subscriber` ORDER BY id DESC LIMIT 1";
	if ($result = \db::query($SQL)) {
		$value = $result[0]["category"];
	}
}

$SQL = "SELECT category FROM `e_subscriber` WHERE category <> '' GROUP BY category ORDER BY category";
$result = \db::query($SQL);
?>
<select placeholder="<?=input("label")?>" name="<?=input("field")?>" id="<?=input("input-id")?>" class="select2">
	<?php
	if ($result) {
		foreach ($result as $row) {
			$selected = ($value && $value == $row["category"]) ? " selected" : "";
			echo '	<option value="' . $row["category"] . '" ' . $selected . '>' . $row["category"] . '</option>' . PHP_EOL;
		}
	}
	?>
</select>
<script>
	$(document).ready(function() {
		$("#<?=input("input-id")?>").select2({
			tags: true
		});
	});
</script>