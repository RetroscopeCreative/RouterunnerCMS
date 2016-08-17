<?php
$bootstrap = \bootstrap::get();
$main_url = "";
$temp_url = array_shift($bootstrap->urls);
if ($temp_url != $bootstrap->resource_url) {
	$main_url = $temp_url;
}

$meta = array_merge(array(
	"social-title" => "",
	"social-image" => "",
	"social-description" => "",
	"social-type" => "",
), $bootstrap->pageproperties["meta"]);

$og_types = array(
	"article",
	"book",
	"profile",
	"video",
	"website",
	"music"
);

$debug = 1;

?>
<!-- BEGIN PAGE PANEL -->
<div class="page-panel height-header">
	<?=\Routerunner\Routerunner::form("frm", $runner, true)?>

</div>
<!-- END PAGE PANEL -->
