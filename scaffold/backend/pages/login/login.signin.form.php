<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.02.18.
 * Time: 14:38
 */
if (\runner::now("newpwd") && \runner::now("newpwd") == "succeed") {
	echo '<div class="alert alert-newpwd alert-success">Your new password has been sent to your e-mail address!</div>';
} elseif (\runner::now("newpwd") && \runner::now("newpwd") == "error") {
	echo '<div class="alert alert-newpwd alert-danger">New password confirmation failed!</div>';
}
?>
<!-- BEGIN LOGIN FORM -->
<form id="<?=\Routerunner\Form::$id?>" action="admin/<?=\bootstrap::get("url")?>" class="login-form" method="post">
	<h3 class="form-title">Login to your account</h3>
	<div class="alert alert-danger display-hide">
		<button class="close" data-close="alert"></button>
		<span>Enter any username and password. </span>
	</div>
	<?=form::input("email")?>
	<?=form::input("password")?>
	<div class="form-actions">
		<?=form::input("rememberme")?>
		<?=form::input("submitbtn")?>
	</div>
	<?php
	/*
	<div class="login-options">
		<h4>Or login with</h4>
		<ul class="social-icons">
			<?=form::input("fbconnect")?>
			<?=form::input("twconnect")?>
			<?=form::input("gpconnect")?>
			<?=form::input("inconnect")?>
		</ul>
	</div>
	*/
	?>
	<div class="forget-password">
		<h4>Forgot your password ?</h4>
		<p>no worries, click <a href="javascript:;" id="forget-password" style="text-decoration: underline;">here</a> to reset your password.</p>
	</div>
</form>
<!-- END LOGIN FORM -->
