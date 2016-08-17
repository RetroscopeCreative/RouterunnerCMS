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
	<div class="row">
		<div class="col-md-4">
			<div class="row">
				<div class="col-md-12">
					<?=form::input("label")?>
				</div>
				<div class="col-md-12">
					<?=form::input("category")?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<?=form::input("subject")?>
				</div>
				<div class="col-md-12">
					<?=form::input("mail_route")?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<?=form::input("active")?>
				</div>
				<div class="col-md-4 col-md-push-4">
					<?=form::input("submit")?>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-12">
					<?=form::input("mail_html")?>
				</div>
				<div class="col-md-12">
					<?=form::input("mail_text")?>
				</div>
			</div>
		</div>
		<div class="col-md-2">
			<p>Usable subscriber fields in letter:<br>
				[id] - identity number<br>
				[name] - name<br>
				[email] - e-mail address<br>
				[link] - webpage or link<br>
				[category] - the category to subscribe for<br>
				[subscribe_date] - the date and time of subscribe<br>
				[unsubscribe] - the link of unsubscribe<br>
			</p>
		</div>
	</div>
</form>
