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
?>

<form class="row client-row" id="client-search-form">
	<div class="col-md-2"><strong>Search:</strong></div>
	<div class="col-md-4"><input type="text" name="search" value="<?=$search?>" /></div>
	<div class="col-md-2"><input type="submit" class="btn" value="search" /></div>
</form>
<div class="row client-row">
	<div class="col-md-2"><strong>activity date<br>activity method</strong></div>
	<div class="col-md-2"><strong>clicked link</strong></div>
	<div class="col-md-2"><strong>subscriber name<br>subscriber e-mail</strong></div>
	<div class="col-md-2"><strong>subscriber category</strong></div>
	<div class="col-md-2"><strong>newsletter send date<br>unsubscribe date</strong></div>
	<div class="col-md-2"><strong>campaign label<br>campaign subject</strong></div>
</div>
