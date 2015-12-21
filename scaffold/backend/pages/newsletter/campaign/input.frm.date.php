<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.07.13.
 * Time: 11:27
 */

$value = "";
if (input("value") && is_numeric(input("value"))) {
	$value = strftime("%Y-%m-%d %H:%M:%S", input("value"));
}
 ?>
<div class="form-group">
	<label for="<?=input("input-id")?>"><?=input("label")?></label>
	<input type="text" placeholder="<?=input("label")?>" class="form-control" name="<?=input("field")?>" id="<?=input("input-id")?>" value="<?=$value?>"/>
</div>

<script>
	$(document).ready(function() {
		$('#<?=input("input-id")?>').datetimepicker({
			format: "YYYY-MM-DD HH:mm:ss"
		});
	});
</script>