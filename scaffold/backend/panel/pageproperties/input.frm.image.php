<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.05.08.
 * Time: 9:00
 */
 
 ?>
<div class="form-group form-md-line-input input-data-holder <?=input("class")?>" data-routerunner-input='<?=input("data")?>'>
	<?php
	if (input("value")) {
		?>
		<img src="<?= (input("value") ? input("value") : "#") ?>" name="<?= input("field") ?>"
			 id="<?= input("input-id") ?>" class="pageprop-input form-control <?= input("inner-class") ?>"/>
	<?php
	}
	?>
	<label for="<?=input("input-id")?>"><?=input("label")?></label>
	<?=input("help")?>
</div>
