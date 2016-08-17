<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.07.13.
 * Time: 11:27
 */
 
 ?>

<div class="checkbox">
	<label for="<?=input("input-id")?>"><input type="checkbox" name="<?=input("field")?>" id="<?=input("input-id")?>" value="1" <?=(input("value") == 1) ? "checked" : ""?>/> <?=input("label")?></label>
</div>



