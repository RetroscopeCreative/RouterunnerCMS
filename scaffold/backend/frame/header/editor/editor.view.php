<!-- BEGIN HEADER TOP -->
<div class="page-header-top page-header-to-fixed">
	<div class="container-fluid row">
		<!-- BEGIN ACTION PANEL -->
		<div id="routerunner-action-panel" class="col-xs-10 col-sm-10 col-md-6 col-lg-5 pull-right">
			<?php
			\runner::route('/backend/panel/action');
			?>
		</div>
		<!-- END ACTION PANEL -->
		<!-- BEGIN PAGE PROPERTIES -->
		<div id="routerunner-pageproperties-panel" class="col-xs-2 col-sm-2 col-md-6 col-lg-7" style="display: none">
			<?php
			\runner::route('/backend/panel/pageproperties');
			?>
		</div>
		<!-- END PAGE PROPERTIES -->
	</div>
</div>
<!-- END HEADER TOP -->
