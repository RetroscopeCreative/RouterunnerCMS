<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.07.13.
 * Time: 11:24
 */
$url = \bootstrap::get("url");
?>
<form method="post" id="client-form" role="form">
	<?=form::input("id")?>
	<?=form::input("campaign")?>
	<?=form::input("nonce")?>
	<div class="row">
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-12">
					<?=form::input("label")?>
				</div>
				<div class="col-md-12">
					<?=form::input("category")?>
				</div>
				<div class="col-md-12">
					<?=form::input("active")?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<?=form::input("start")?>
				</div>
				<div class="col-md-6">
					<?=form::input("finish")?>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-12">
					<?=form::input("test_address")?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<?=form::input("limit_per_period")?>
				</div>
				<div class="col-md-6">
					<?=form::input("period")?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4 col-md-push-8">
					<?=form::input("submit")?>
				</div>
			</div>
		</div>
	</div>
</form>
