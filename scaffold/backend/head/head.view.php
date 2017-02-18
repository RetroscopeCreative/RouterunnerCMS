<!-- BEGIN HEAD -->
<head>
	<meta charset="utf-8"/>
	<title><?php echo \runner::config("TITLE"); ?></title>
	<base href="<?php echo \runner::config("BASE"); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<meta content="" name="description"/>
	<meta content="" name="author"/>

	<!-- BEGIN GLOBAL MANDATORY STYLES -->
	<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css">
	<link href="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link href="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css">
	<link href="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css">
	<!-- END GLOBAL MANDATORY STYLES -->
	<!-- BEGIN THEME STYLES -->
	<link href="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/css/components-md.css" id="style_components" rel="stylesheet" type="text/css">
	<link href="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/css/plugins-md.css" rel="stylesheet" type="text/css">
	<link href="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/admin/layout3/css/layout.css" rel="stylesheet" type="text/css">
	<link href="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/admin/layout3/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color">
	<link href="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/admin/layout3/css/custom.css" rel="stylesheet" type="text/css">
	<!-- END THEME STYLES -->

	<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
	<script src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>


	<!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
	<link rel="stylesheet" type="text/css" href="<?php echo \runner::config("BACKEND_ROOT"); ?>backend/thirdparty/select2/dist/css/select2.min.css"/>
	<!-- END PAGE LEVEL PLUGIN STYLES -->
	<!-- BEGIN PAGE STYLES -->
	<link  rel="stylesheet" type="text/css" href="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/admin/pages/css/tasks.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/jstree/dist/themes/default/style.min.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/plugins/dropzone/css/dropzone.css"/>
	<style>
		.dropzone .dz-preview .dz-details img, .dropzone-previews .dz-preview .dz-details img {
			height: 100px !important;
		}
	</style>
	<!-- END PAGE STYLES -->
	<!-- BEGIN THIRD PARTY STYLES -->
	<link rel="stylesheet" type="text/css" href="<?php echo \runner::config("BACKEND_ROOT"); ?>backend/thirdparty/datepicker/bootstrap-material-datepicker.css"/>
	<!-- END THIRD PARTY STYLES -->


	<link rel="shortcut icon" href="favicon.ico"/>

	<link href="<?php echo \runner::config("BACKEND_ROOT"); ?>backend/css/frame.css" rel="stylesheet" type="text/css">

	<?php
    if (\runner::now("page")) {
        \runner::route(str_replace("pages/", "", \runner::now("page")));
    }
	?>

	<script>
		var routerunner_models = [];
		var routerunner_attach = function(selector) {
			window.routerunner_models.push(selector);
		};
	</script>
</head>
<!-- END HEAD -->
