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

\runner::route("/backend/pages/user/memberform", array("profile" => \user::me()));
