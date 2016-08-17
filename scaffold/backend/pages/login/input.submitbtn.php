<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.02.18.
 * Time: 14:45
 */
$debug = 1;
?>
<input type="hidden" name="<?=$this->formname?>" value="<?=input("value")?>" />
<button type="submit" id="<?=input("id")?>" class="btn green-haze pull-right">
	<?=input("value")?> <i class="m-icon-swapright m-icon-white"></i>
</button>
