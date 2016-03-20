<?php
$debug = 1;
$url = \bootstrap::get("url");

$backend_uri = false;
$backend_session = false;
$code = false;
backend_mode($backend_uri, $backend_session, $code);

unset($_GET[$backend_uri]);
$url .= '?' . http_build_query($_GET);
?>

<body class="page-md page-header-top-fixed">
<div class="backend-wrapper">
<?php
\runner::route('/backend/frame/header');
?>

<!-- BEGIN CONTAINER -->
<div class="page-container row">
	<!-- BEGIN PAGE CONTENT-->
	<div class="routerunner-content">
		<div class="routerunner-content-wrapper">
			<iframe src="<?=$url?>&<?=$backend_uri?>=<?=$code?>" class="content-iframe" id="routerunner-content-iframe" style="border: 0;" frameborder="0"></iframe>
		</div>
	</div>
	<!-- END PAGE CONTENT-->
	<!-- BEGIN ROUTERUNNER PANEL -->
	<div id="routerunner-panel" class="routerunner-panel-wrapper hidden">
		<div id="routerunner-panel-titlebar">
			<div class="buttons">
				<a href="javascript:;" id="routerunner-expand-panel-btn" class="btn btn-icon-only btn-circle default tooltips" data-container="body" data-placement="bottom" data-html="true" data-original-title="expand panel">
					<span class="fa fa-expand"></span>
				</a>
				<a href="javascript:;" id="routerunner-lock-panel-btn" class="btn btn-icon-only btn-circle default tooltips" data-container="body" data-placement="bottom" data-html="true" data-original-title="lock panel">
					<span class="fa fa-lock"></span>
				</a>
				<a href="javascript:;" id="routerunner-place-panel-btn" class="btn btn-icon-only btn-circle default tooltips" data-container="body" data-placement="bottom" data-html="true" data-original-title="place panel to the other side">
					<span class="fa fa-arrow-left"></span>
				</a>
			</div>
			<h3>Routerunner model panel</h3>
		</div>
		<div id="routerunner-changes-panel" style="display: none;">
		<?php
		\runner::route('/backend/panel/changes');
		?>
		</div>
		<div id="routerunner-modelselector-panel">
		<?php
		\runner::route('/backend/panel/modelselector');
		?>
		</div>
		<div id="routerunner-model-panel">
		<?php
		\runner::route('/backend/panel/model');
		?>
		</div>
	</div>
	<!-- END ROUTERUNNER PANEL -->


</div>
<!-- END CONTAINER -->

<?php
\runner::route('/backend/frame/footer');
?>
</div>
