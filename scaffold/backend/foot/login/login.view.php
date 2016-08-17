<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/respond.min.js"></script>
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/excanvas.min.js"></script>
<![endif]-->
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo \runner::config("BACKEND_ROOT"); ?>backend/thirdparty/select2/dist/js/select2.full.min.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/admin/pages/scripts/login.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
	jQuery(document).ready(function() {
		Metronic.init(); // init metronic core components
		Layout.init(); // init current layout
		Login.init();

		if ($(".alert-forgotten").length) {
			$("#forget-password").trigger("click");
		};
	});
</script>
<!-- END JAVASCRIPTS -->

<script type="text/javascript" src="<?php echo \runner::config("BACKEND_ROOT"); ?>assets/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="<?php echo \runner::config("BACKEND_ROOT"); ?>assets/js/modernizr.custom.js"></script>
