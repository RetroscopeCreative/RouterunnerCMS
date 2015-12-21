<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.07.13.
 * Time: 11:10
 */

$bg = "bg-default";
switch (\model::property("activity")) {
	case "open":
		$bg = "bg-info";
		break;
	case "click":
		$bg = "bg-success";
		break;
	case "unsubscribe":
		$bg = "bg-danger";
		break;
}
?>

<div class="row client-row <?php echo $bg; ?>">
	<div class="col-md-2"><?php echo strftime("%Y-%m-%d %H:%M:%S", \model::property("activity_date")); ?><br><strong><?php echo \model::property("activity"); ?></strong></div>
	<div class="col-md-2"><?php echo stripslashes(\model::property("clicked")); ?></div>
	<div class="col-md-2"><?php echo \model::property("name"); ?><br><strong><?php echo \model::property("email"); ?></strong></div>
	<div class="col-md-2"><?php echo \model::property("category"); ?></div>
	<div class="col-md-2"><?php echo strftime("%Y-%m-%d %H:%M:%S", \model::property("send_date")); ?><br><strong><?php echo (\model::property("unsubscribe_date") ? strftime("%Y-%m-%d %H:%M:%S", \model::property("unsubscribe_date")) : ""); ?></strong></div>
	<div class="col-md-2"><?php echo \model::property("campaign_label"); ?><br><?php echo \model::property("subject"); ?></div>
</div>
