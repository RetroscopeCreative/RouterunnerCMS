<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.07.13.
 * Time: 11:11
 */
$search = (isset($_GET["subscriber_search"]) ? $_GET["subscriber_search"] : "");
?>
<form class="row client-row" id="client-search-form">
	<div class="col-md-2"><strong>Search:</strong></div>
	<div class="col-md-4"><input type="text" name="subscriber_search" value="<?=$search?>" /></div>
	<div class="col-md-2"><input type="submit" class="btn" value="search" /></div>
	<div class="col-md-2 col-md-offset-2"><a href="admin/newsletter/subscriber" class="btn btn-primary">New subscriber</a></div>
</form>
<div class="row client-row">
	<div class="col-md-1"><strong>id</strong></div>
	<div class="col-md-2"><strong>category</strong></div>
	<div class="col-md-2"><strong>label</strong></div>
	<div class="col-md-3"><strong>link</strong></div>
	<div class="col-md-3"><strong>email</strong></div>
	<div class="col-md-1">&nbsp;</div>
</div>
<div class="row client-row">
	<?php
	echo \Routerunner\Routerunner::form("frm", $runner, true);
	?>
</div>

