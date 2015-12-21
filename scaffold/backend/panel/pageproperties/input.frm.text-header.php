<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.05.07.
 * Time: 21:30
 */
 ?>
<div class="form-group input-data-holder <?=input("class")?>" data-routerunner-input='<?=input("data")?>'>
	<div class="input-group">
		<span class="input-group-addon input-circle-left tooltips" data-container="body" data-placement="bottom" data-html="true" data-original-title="<?=input("label")?>">
			<?=input("icon")?>
		</span>
		<input type="text" name="<?=input("field")?>" id="<?=input("input-id")?>" class="<?=input("class-inner")?> pageprop-input form-control input-circle-right tooltips" placeholder="<?=input("label")?>" data-container="body" data-placement="bottom" data-html="true" data-original-title="<?=input("label")?>" value="<?=input("value")?>" />
	</div>
</div>
