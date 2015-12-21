<body class="login">
<!-- BEGIN LOGO -->
<div class="logo">
	<a href="index.php">
		<img src="<?php echo \runner::config("BACKEND_ROOT"); ?>backend/img/logo/routerunner-cms-invert.png" alt="Routerunner CMS" width="300"/>
	</a>
</div>
<!-- END LOGO -->
<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
<div class="menu-toggler sidebar-toggler">
</div>
<!-- END SIDEBAR TOGGLER BUTTON -->
<!-- BEGIN LOGIN -->
<div class="content">
	<?php
	echo \Routerunner\Routerunner::form("signin", $runner, true) . PHP_EOL;
	echo \Routerunner\Routerunner::form("forgotten", $runner, true) . PHP_EOL;
	?>
</div>
<!-- END LOGIN -->
<!-- BEGIN COPYRIGHT -->
<div class="copyright">
	2015 &copy; Retroscope Creative.
</div>
<!-- END COPYRIGHT -->
