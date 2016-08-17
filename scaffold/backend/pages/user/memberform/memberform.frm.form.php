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
			echo form::input("pwd");
			echo form::input("usergroup");
			?>
		</div>
		<div class="col-md-4">
			<?php
			echo form::input("name");
			echo form::input("pwd_confirm");
			echo form::input("licence");
			?>
		</div>
		<div class="col-md-4">
			<?php
			echo form::input("scope");
			echo form::input("submit");
			?>
		</div>
	</div>
</form>
