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
	<div class="col-md-1">
		<?=form::input("nonce")?>
		<?=form::input("id")?>
	</div>
	<div class="col-md-2">
		<?=form::input("category")?>
	</div>
	<div class="col-md-2">
		<?=form::input("label")?>
	</div>
	<div class="col-md-3">
		<?=form::input("link")?>
	</div>
	<div class="col-md-3">
		<?=form::input("email")?>
	</div>
	<div class="col-md-1">
		<?=form::input("submit")?>
	</div>
</form>
