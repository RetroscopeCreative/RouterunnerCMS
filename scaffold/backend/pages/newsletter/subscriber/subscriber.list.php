<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.07.13.
 * Time: 11:10
 */
?>
<div class="row client-row">
	<div class="col-md-1"><?php echo \model::property("id"); ?></div>
	<div class="col-md-2"><?php echo \model::property("category"); ?></div>
	<div class="col-md-2"><?php echo \model::property("label"); ?></div>
	<div class="col-md-3"><?php echo \model::property("link"); ?></div>
	<div class="col-md-3"><?php echo \model::property("email"); ?></div>
	<div class="col-md-1"><a href="admin/newsletter/subscriber/?id=<?php echo \model::property("id"); ?>" class="btn btn-primary">Modify</a></div>
</div>
