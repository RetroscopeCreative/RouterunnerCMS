<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.07.13.
 * Time: 11:24
 */
$url = \bootstrap::get("url");
?>
<form method="post" id="client-form">
	<?=form::input("nonce")?>
	<?=form::input("id")?>
	<?=form::input("reg_date")?>
	<?=form::input("confirm_date")?>
	<div class="row">
		<div class="col-md-4">
			<?php
			echo form::input("email");
			?>
		</div>
		<div class="col-md-4">
			<?php
			echo form::input("name");
			?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<?php echo form::input("pwd"); ?>
		</div>
		<div class="col-md-4">
			<?php echo form::input("pwd_confirm"); ?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<?php
			echo form::input("usergroup");
			?>
		</div>
		<div class="col-md-4">
			<?php
			echo form::input("licence");
			?>
		</div>
		<div class="col-md-2 col-md-push-2">
			<?php
			echo form::input("submit");
			?>
		</div>
	</div>
</form>
