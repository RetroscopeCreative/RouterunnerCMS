<?php

session_start();

require '../../runner-config.php';
require '../Routerunner/Routerunner.php';
use \Routerunner\Routerunner as runner;


new runner(array(
	'mode' => 'nl',
	'silent' => true,
	'method' => 'get',
	'resource' => '/nl/',
	'bootstrap' => false,
));

$e = (isset($_GET["p"]) ? $_GET["p"] : false);

if ($e) {
	$SQL_req = "INSERT INTO e_request (request, date) VALUES (:request, :date)";
	\db::insert($SQL_req, array(
		":request" => $e,
		":date" => time(),
	));

	$e_camp = explode("/", $e);
	$e_str = $e;
	if (is_array($e_camp) && count($e_camp) >= 3) {
		$e_camp = array();
		$pos = 0;
		while (preg_match('~[/]~', $e, $preg, PREG_OFFSET_CAPTURE, $pos) && count($e_camp) < 3) {
			$pos = $preg[0][1];
			if (substr($e, $pos-1, 1) != "\\") {
				$e_camp[] = urldecode(substr($e, 0 , $pos));
				$e = substr($e, $pos+1);
				$pos = 0;
			} else {
				$pos++;
			}
		}
		if ($e) {
			$e_camp[] = urldecode($e);
		}
		$method = $e_camp[0];
		$deliver = hexdec($e_camp[1]);
		$hash = stripslashes($e_camp[2]);
		$hashb64 = base64_decode(str_replace(',', '/', $e_camp[2]));
		$click_url = "";
		if (isset($e_camp[3])) {
			$click_url = $e_camp[3];
		}
		$SQL = "SELECT address_id, uhash FROM e_delivered WHERE id = :deliver";
		if (($delivered = \db::query($SQL, array(":deliver" => $deliver))) && isset($delivered[0]["uhash"])
			&& (\Routerunner\Crypt::checker($delivered[0]["uhash"], $hash)
				|| \Routerunner\Crypt::checker($delivered[0]["uhash"], $hashb64))
		) {

			$stat_params = array(
				":date" => time(),
				":deliver_id" => $deliver,
				":method" => $method,
				":click" => $click_url,
				":referer" => ((isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : ((isset($_SERVER["HTTP_FROM"])) ? $_SERVER["HTTP_FROM"] : null)),
				":ip" => $_SERVER["REMOTE_ADDR"],
				":ua" => $_SERVER["HTTP_USER_AGENT"],
				":cookie" => print_r($_COOKIE, true),
				":server" => print_r($_SERVER, true),
			);
			$SQL_stat = "INSERT INTO e_stat (date, deliver_id, method, click, ip, useragent, referer, cookie, server) VALUES (:date, :deliver_id, :method, :click, :ip, :ua, :referer, :cookie, :server)";
			\db::insert($SQL_stat, $stat_params);
			if ($method == "unsubscribe" && isset($delivered[0]["address_id"])) {
				$SQL_unsubscribe = "UPDATE e_subscriber SET unsubscribe = :time WHERE id = :id";
				\db::query($SQL_unsubscribe, array(
					":time" => time(),
					":id" => $delivered[0]["address_id"],
				));
				$isOk = false;
				$SQL_check = "SELECT unsubscribe FROM e_subscriber WHERE id = :id";
				if (($check_result = \db::query($SQL_check, array(":id" => $delivered[0]["address_id"])))
					&& isset($check_result[0]["unsubscribe"]) && !is_null($check_result[0]["unsubscribe"])) {
					$isOk = true;
				}

				if ($isOk) {
					$url = \runner::config("BASE") . "unsubscribe/success";
				} else {
					$url = \runner::config("BASE") . "unsubscribe/error";
				}
				header("Location: " . $url);
			} elseif ($method == "open") {
				try {
					$im = @imagecreatetruecolor(1, 1);
					@imagealphablending($im, true);
					$transparent = @imagecolorallocatealpha($im, 0, 0, 0, 127);
					@imagefilledrectangle($im, 0, 0, 1, 1, $transparent);
					@header('Content-type: image/png');
					@imagepng($im);
					@imagedestroy($im);
				} catch (Exception $e) {}
			} elseif ($method == "click") {
				$url = stripslashes($click_url);
				if (!preg_match('~^http(s)?:\/\/~', $url)) {
					$url = \runner::config("BASE") . $url;
				}
				header("Location: " . $url);
			}
		} elseif ($click_url) {
			$url = stripslashes($click_url);
			if (!preg_match('~^http(s)?:\/\/~', $url)) {
				$url = \runner::config("BASE") . $url;
			}
			header("Location: " . $url);
		} else {
			header("Location: " . \runner::config("BASE"));
		}
	}
}

$debug = 1;
