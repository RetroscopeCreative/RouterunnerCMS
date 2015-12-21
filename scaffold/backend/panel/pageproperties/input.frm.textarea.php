<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.05.07.
 * Time: 19:23
 */
$debug = 1;
 ?>
<div class="form-group form-md-line-input input-data-holder" data-routerunner-input='<?=input("data")?>'>
	<textarea name="<?=input("field")?>" id="<?=input("id")?>" class="pageprop-input form-control" rows="2"><?=input("value")?></textarea>
	<label for="<?=input("id")?>"><?=input("label")?></label>
	<?=input("help")?>
</div>
