<!-- BEGIN JAVASCRIPTS (Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="<?php echo \runner::config('BACKEND_ROOT'); ?>metronic/assets/global/plugins/respond.min.js"></script>
<script src="<?php echo \runner::config('BACKEND_ROOT'); ?>metronic/assets/global/plugins/excanvas.min.js"></script>
<![endif]-->
<!--
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
//-->
<!-- IMPORTANT! Load jquery-ui.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>backend/thirdparty/metronic/custom-layout.js" type="text/javascript"></script>
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/admin/layout3/scripts/demo.js" type="text/javascript"></script>

<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>backend/thirdparty/jquery.cookie.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
	jQuery(document).ready(function() {
		Metronic.init(); // init metronic core componets
		Layout.init(); // init layout
		Demo.init(); // init demo(theme settings page)
	});
</script>
<!-- END JAVASCRIPTS -->

<script type="text/javascript" src="<?php echo \runner::config("BACKEND_ROOT"); ?>backend/js/baserunner.js?version=1.3.1"></script>
<script type="text/javascript" src="<?php echo \runner::config("BACKEND_ROOT"); ?>backend/js/routerunner-cms.js?version=1.3.1"></script>
<script type="text/javascript">
	routerunner.settings = $.extend({}, <?php echo json_encode(\Routerunner\Config::$defaults); ?>, routerunner.settings);
</script>


<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/jstree/dist/jstree.min.js"></script>

<script type="text/javascript" src="<?php echo \runner::config("BACKEND_ROOT"); ?>backend/thirdparty/select2/dist/js/select2.full.min.js"></script>
<script type="text/javascript" src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>

<script type="text/javascript" src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/dropzone/dropzone.js"></script>
<script type="text/javascript" src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/jquery.blockui.min.js"></script>


<!-- END PAGE LEVEL PLUGINS -->

<!-- BEGIN THIRD PARTY SCRIPTS -->
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>backend/thirdparty/accursoft-caret/jquery.caret.js" type="text/javascript"></script>
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>backend/thirdparty/underscore-min.js" type="text/javascript"></script>

<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>backend/thirdparty/datepicker/moment-with-locales.js" type="text/javascript"></script>
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>backend/thirdparty/datepicker/bootstrap-material-datepicker.js" type="text/javascript"></script>
<!-- END THIRD PARTY SCRIPTS -->

<?php
if (\runner::now("page")) {
    \runner::route(str_replace("pages/", "", \runner::now("page")));
}
?>
</body>