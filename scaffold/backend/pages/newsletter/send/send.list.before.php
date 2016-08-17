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
</form>
<div class="row client-row">
	<div class="col-md-1"><strong>job id<br>campaign id</strong></div>
	<div class="col-md-2"><strong>label<br>category</strong></div>
	<div class="col-md-1"><strong>active</strong></div>
	<div class="col-md-1"><strong>sent<br>to send</strong></div>
	<div class="col-md-2"><strong>job started<br>job finished</strong></div>
	<div class="col-md-1"><strong>limit</strong></div>
	<div class="col-md-2"><strong>test addresses</strong></div>
	<div class="col-md-1">&nbsp;</div>
	<div class="col-md-1">&nbsp;</div>
</div>
