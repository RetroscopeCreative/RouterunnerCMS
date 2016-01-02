<?php
session_cache_limiter(false);
session_start();

header('Content-type: text/html; charset=utf-8');
// bench start
$bench = array("start"=>array("mem"=>memory_get_usage(), "peak"=>memory_get_peak_usage(true), "time"=>microtime(true)));

require '../../runner-config.php';

require '../Routerunner/Routerunner.php';
use \Routerunner\Routerunner as runner;

$runner = false;
if (isset($_GET["runner"])) {
	$runner = json_decode(base64_decode($_GET["runner"]), true);
}
if (!is_array($runner)) {
	$runner = array();
}

$host = $_SERVER['HTTP_HOST'];
$root = 'backend';
if (isset($_GET['root'])) {
	$root = $_GET['root'];
	$_SESSION['root'] = $_GET['root'];
} elseif (isset($_SESSION['root'])) {
	$root = $_SESSION['root'];
}

$scaffold = '../scaffold';
$tree = (@include $scaffold . '/model/tree.php');

$runner = array_merge(array(
	//'version' => array('1.1', '1.2', 'menu.pre/0.9'),
	'mode' => 'backend',
	'root' => $root,
	'tree' => $tree,
	//'silent' => true,
	'language' => 1,

	'SITE' => '',
	'SITENAME' => '',
	'BASE' => 'http://' . $_SERVER['HTTP_HOST'] . '/',

	'DB_HOST' => '',
	'DB_NAME' => '',
	'DB_USER' => '',
	'DB_PASS' => '',

), $runner);

new runner($runner);

// bench over
$bench["end"] = array("mem"=>memory_get_usage(), "peak"=>memory_get_peak_usage(true), "time"=>microtime(true));
$bench["diff"] = array("mem"=>$bench["end"]["mem"]-$bench["start"]["mem"], "peak"=>$bench["end"]["peak"]-$bench["start"]["peak"], "time"=>$bench["end"]["time"]-$bench["start"]["time"]);
//$bench["load"] = sys_getloadavg();
//echo "<!--".print_r($bench["diff"], true)."\n".print_r($bench["load"],true)."//-->";
echo "<!--".print_r($bench["diff"], true)."\n//-->";