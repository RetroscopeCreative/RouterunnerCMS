<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.07.13.
 * Time: 11:10
 */
if (\runner::now("e_cron_running")) {
	?>
<div class="row client-row">
	<div class="col-md-12 bg-danger">
		<h3>
			Newsletter job is active with process id: <?php echo \runner::now("e_cron_running"); ?><br>
		</h3>
		<p>
			Wait for finish or if you want to stop, open and click on "Stop immediately" button!
		</p>
	</div>
</div>
	<?php
}
?>

<div class="row client-row">
	<div class="col-md-1"><?php echo \model::property("id"); ?><br><?php echo \model::property("campaign_id"); ?></div>
	<div class="col-md-2" style="word-wrap: break-word;"><?php echo \model::property("label"); ?><br><?php echo \model::property("category"); ?></div>
	<div class="col-md-1"><?php echo (\model::property("active") == "1" ? "yes" : "no"); ?></div>
	<div class="col-md-1"><?php echo \model::property("sent"); ?><br><?php echo \model::property("to_send"); ?></div>
	<div class="col-md-2"><?php echo \model::property("start"); ?><br><?php echo \model::property("finish"); ?></div>
	<div class="col-md-1"><?php echo \model::property("limit"); ?></div>
	<div class="col-md-2"><?php echo \model::property("test_address"); ?></div>
	<div class="col-md-1"><a href="admin/newsletter/campaign/?id=<?php echo \model::property("campaign_id"); ?>" class="btn btn-primary">Modify campaign</a></div>
	<div class="col-md-1"><a href="admin/newsletter/send/?id=<?php echo \model::property("id"); ?>&cid=<?php echo \model::property("campaign_id"); ?>" class="btn btn-success">Send campaign</a></div>
</div>
