<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.07.13.
 * Time: 11:11
 */
$bootstrap = \bootstrap::get();
$params = $bootstrap->params;

$search = (isset($_GET["search"]) ? $_GET["search"] : "");

\runner::route("/backend/pages/user/memberform");

?>

<form class="row client-row" id="search-form">
	<div class="col-md-2"><strong>Search:</strong></div>
	<div class="col-md-4"><input type="text" name="search" value="<?=$search?>" /></div>
	<div class="col-md-2"><input type="submit" class="btn" value="search" /></div>
	<div class="col-md-2 col-md-offset-2"><a href="admin/user/member?id=0" class="btn btn-primary">New member</a></div>
</form>
<div class="row client-row">
	<div class="col-md-1"><strong>id</strong></div>
	<div class="col-md-3"><strong>email</strong></div>
	<div class="col-md-2"><strong>registration<br>confirmation</strong></div>
	<div class="col-md-2"><strong>last login date<br>last ip address</strong></div>
	<div class="col-md-3"><strong>usergroup<br>licence</strong></div>
	<div class="col-md-1">&nbsp;</div>
</div>
