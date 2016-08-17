<?php
$debug = 1;
?>
<!-- BEGIN HEADER -->
<div class="page-header routerunner-framework">
	<!-- BEGIN HEADER MENU -->
	<div class="page-header-menu page-header-top">
		<div class="container-fluid">
			<!-- BEGIN LOGO -->
			<div class="page-logo">
				<a href="index.html" class="logo">
					<img src="<?php echo \runner::config("BACKEND_ROOT"); ?>backend/img/logo/routerunner-cms-stamp-invert.png" alt="Routerunner CMS" class="logo-default" height="40"/>
				</a>
			</div>
			<!-- END LOGO -->

			<!-- BEGIN RESPONSIVE MENU TOGGLER -->
			<a href="javascript:;" class="menu-toggler"></a>
			<!-- END RESPONSIVE MENU TOGGLER -->

			<!-- BEGIN TOP NAVIGATION MENU -->
			<div class="top-menu">
				<ul class="nav navbar-nav pull-right">
					<li class="droddown dropdown-separator">
						<span class="separator"></span>
					</li>
					<?php
					\runner::route("languages");
					?>
					<li class="droddown dropdown-separator">
						<span class="separator"></span>
					</li>
					<?php
					\runner::route("usermenu");
					?>
				</ul>
			</div>
			<!-- END TOP NAVIGATION MENU -->

			<?php
			\runner::route('/backend/panel/menu');
			?>
		</div>
	</div>
	<!-- END HEADER MENU -->
	<?php
	if (\runner::now("page") == "editor") {
		\runner::route('editor');
	}
	?>
</div>
<!-- END HEADER -->
