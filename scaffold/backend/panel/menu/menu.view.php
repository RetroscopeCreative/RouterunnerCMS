<?php
$badges = array();
if (true) { // auth to users
	$badges[] = "(SELECT COUNT(*) FROM member) AS users";
	$badges[] = "(SELECT COUNT(*) FROM {PREFIX}usergroup) AS usergroups";
}
if (true) { // auth to newsletter
	$badges[] = "(SELECT COUNT(*) FROM e_campaign) AS campaigns";
}
$badges_data = array(
	"users" => "x",
	"usergroups" => "x",
	"campaigns" => "x",
);
if ($badges) {
	$SQL = "SELECT " . implode(", ", $badges);
	if ($badges_result = \db::query($SQL)) {
		$badges_data = array_merge($badges_data, $badges_result[0]);
	}
}
?>
<div id="routerunner-menu-panel" class="hor-menu ">
	<ul class="nav navbar-nav">
		<li class="menu-dropdown classic-menu-dropdown ">
			<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
				<i class="fa fa-home"></i>
				Routerunner CMS <i class="fa fa-angle-down"></i>
			</a>
			<ul class="dropdown-menu pull-left">
				<li>
					<a href="home" target="_blank">
						<i class="fa fa-globe"></i>
						Visitor mode </a>
				</li>
				<li>
					<a href="admin">
						<i class="fa fa-pencil"></i>
						Editor mode </a>
				</li>
				<li class=" dropdown-submenu">
					<a href="javascript:;">
						<i class="fa fa-user"></i>
						Users </a>
					<ul class="dropdown-menu">
						<li class=" ">
							<a href="admin/user/member?id=0">
								New user </a>
						</li>
						<li class=" ">
							<a href="admin/user/member">
								Users <span class="badge badge-roundless badge-danger"><?php echo $badges_data["users"]; ?></span>
							</a>
						</li>
						<li class=" ">
							<a href="admin/user/group">
								Usergroups <span class="badge badge-roundless badge-danger"><?php echo $badges_data["usergroups"]; ?></span>
							</a>
						</li>
					</ul>
				</li>
				<li class=" dropdown-submenu">
					<a href="javascript:;">
						<i class="fa fa-envelope"></i>
						Newsletter </a>
					<ul class="dropdown-menu">
						<li class=" ">
							<a href="admin/newsletter/campaign?id=0">
								New campaign </a>
						</li>
						<li class=" ">
							<a href="admin/newsletter/send">
								Send campaign
							</a>
						</li>
						<li class=" ">
							<a href="admin/newsletter/campaign">
								Campaigns <span class="badge badge-roundless badge-danger"><?php echo $badges_data["campaigns"]; ?></span>
							</a>
						</li>
						<li class=" ">
							<a href="admin/newsletter/subscriber">
								Subscribers
							</a>
						</li>
						<li class=" ">
							<a href="admin/newsletter/stat">
								Statistics </a>
						</li>
					</ul>
				</li>
				<li>
					<a href="#">
						<i class="fa fa-facebook-square"></i>
						Facebook admin </a>
				</li>
			</ul>
		</li>
		<?php
		\runner::route("/~admin/adminmenu");
		?>
	</ul>
</div>
<!-- END MEGA MENU -->
