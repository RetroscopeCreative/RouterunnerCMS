<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.07.13.
 * Time: 11:11
 */
$bootstrap = \bootstrap::get();
$params = $bootstrap->params;

$search = (isset($_GET["campaign_search"]) ? $_GET["campaign_search"] : "");
if ($params && isset($params["id"])) {
	echo '<div class="row client-row" style="padding: 30px; background-color: #ccc;">' . PHP_EOL;
	echo \Routerunner\Routerunner::form("frm", $runner, true);
	echo '</div>' . PHP_EOL;
}
?>

<form class="row client-row" id="client-search-form">
	<div class="col-md-2"><strong>Search:</strong></div>
	<div class="col-md-4"><input type="text" name="campaign_search" value="<?=$search?>" /></div>
	<div class="col-md-2"><input type="submit" class="btn" value="search" /></div>
	<div class="col-md-2 col-md-offset-2"><a href="admin/newsletter/campaign/?id=0" class="btn btn-primary">New campaign</a></div>
</form>
<div class="row client-row">
	<div class="col-md-1"><strong>id</strong></div>
	<div class="col-md-2"><strong>label</strong></div>
	<div class="col-md-2"><strong>category</strong></div>
	<div class="col-md-1"><strong>active</strong></div>
	<div class="col-md-1"><strong>sent</strong></div>
	<div class="col-md-1"><strong>to send</strong></div>
	<div class="col-md-1"><strong>opened</strong></div>
	<div class="col-md-1"><strong>clicked</strong></div>
	<div class="col-md-1">&nbsp;</div>
	<div class="col-md-1">&nbsp;</div>
</div>

