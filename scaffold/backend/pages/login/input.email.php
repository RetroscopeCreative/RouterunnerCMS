<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.02.18.
 * Time: 14:45
 */
?>
<div class="form-group">
	<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
	<label for="<?=input("id")?>" class="control-label visible-ie8 visible-ie9"><?=input("label")?></label>
	<div class="input-icon">
		<i class="fa fa-envelope"></i>
		<input class="form-control placeholder-no-fix" type="text" placeholder="<?=input("placeholder")?>" name="<?=input("name")?>" id="<?=input("id")?>" data-field='<?=input("data")?>' value="<?=input("value")?>"/>
	</div>
</div>
