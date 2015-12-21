<!-- BEGIN INBOX DROPDOWN -->
<?php
$email=null;
$name=null;
$group=null;
$_custom=array();
$_scope=null;
$_auth=null;

if (user::me($email, $name, $group, $_custom, $_scope, $_auth)) {
	?>

	<!-- BEGIN USER LOGIN DROPDOWN -->
	<li class="dropdown dropdown-user dropdown-dark">
		<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-close-others="true"  title="<?php echo $email; ?>">
			<img alt="" class="img-circle"
				 src="<?php echo \runner::config("BACKEND_ROOT"); ?>backend/img/avatar/admin.jpg">
			<span class="username username-hide-mobile"><?php echo $name; ?></span>
		</a>
		<ul class="dropdown-menu dropdown-menu-default">
			<li>
				<a href="admin/user/profile">
					<i class="icon-user"></i> My Profile </a>
			</li>
			<li class="divider">
			</li>
			<li>
				<a href="admin/user/logout">
					<i class="icon-key"></i> Log Out </a>
			</li>
		</ul>
	</li>
	<!-- END USER LOGIN DROPDOWN -->
	<?php
}