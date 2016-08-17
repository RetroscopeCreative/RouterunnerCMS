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
	<div class="col-md-3"><?php echo \model::property("email"); ?></div>
	<div class="col-md-2"><?php echo \model::property("reg_date"); ?><br><?php echo \model::property("confirm_date"); ?></div>
	<div class="col-md-2"><?php echo \model::property("last_login"); ?><br><?php echo \model::property("last_ip"); ?></div>
	<div class="col-md-3"><?php echo \model::property("usergroup_label"); ?><br><?php echo (\model::property("licence") ? strftime("%Y-%m-%d %H:%M:%S", \model::property("licence")) : ""); ?></div>
	<div class="col-md-1"><a href="admin/user/member/?id=<?php echo \model::property("id"); ?>" class="btn btn-primary">Modify</a></div>
</div>
