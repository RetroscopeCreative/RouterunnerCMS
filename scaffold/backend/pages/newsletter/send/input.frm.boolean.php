<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.07.13.
 * Time: 11:27
 */
 
$debug = 1;
?>

<div class="checkbox">
	<label for="<?=input("input-id")?>"><input type="checkbox" id="<?=input("input-id")?>" value="1" <?=(input("value") == 1) ? "checked" : ""?>/> <?=input("label")?></label>
	<input type="hidden" name="<?=input("field")?>" id="checkbox-<?=input("field")?>" value="<?=(input("value") == 1) ? "1" : "0"?>" />
</div>
<script>
	$(document).ready(function() {
		$("#<?=input("input-id")?>").on("click", function() {
			$("#checkbox-<?=input("field")?>").val(($(this).is(":checked") ? "1" : "0"));
		});
	});
</script>
