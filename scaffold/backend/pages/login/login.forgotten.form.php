<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.02.18.
 * Time: 14:39
 */
?>
<!-- BEGIN FORGOT PASSWORD FORM -->
<form id="<?=\Routerunner\Form::$id?>" class="forget-form" method="post">
	<h3>Forget Password ?</h3>
	<p>Enter your e-mail address below to reset your password.</p>
	<?=form::input("email")?>
	<div class="form-actions">
		<button type="button" id="back-btn" class="btn">
			<i class="m-icon-swapleft"></i> Back </button>
		<?=form::input("submitbtn")?>
	</div>
</form>
<!-- END FORGOT PASSWORD FORM -->
