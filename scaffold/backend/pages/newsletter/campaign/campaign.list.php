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
	<div class="col-md-2"><?php echo \model::property("label"); ?></div>
	<div class="col-md-2" style="word-wrap: break-word;"><?php echo \model::property("category"); ?></div>
	<div class="col-md-1"><?php echo (\model::property("active") == "1" ? "yes" : "no"); ?></div>
	<div class="col-md-1"><?php echo \model::property("sent"); ?></div>
	<div class="col-md-1"><?php echo \model::property("to_send"); ?></div>
	<div class="col-md-1"><?php echo \model::property("opened"); ?></div>
	<div class="col-md-1"><?php echo \model::property("clicked"); ?></div>
	<div class="col-md-1"><a href="admin/newsletter/campaign/?id=<?php echo \model::property("id"); ?>" class="btn btn-primary">Modify</a></div>
	<div class="col-md-1"><a href="admin/newsletter/send/?id=<?php echo \model::property("id"); ?>" class="btn btn-success">Send</a></div>
</div>
