<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.08.05.
 * Time: 16:43
 */

$address = \runner::config('log.email');
$header = array(
	"Subject" => "menu-card-maker.com - Error",
	"FromName" => "menu-card-maker.com",
);
