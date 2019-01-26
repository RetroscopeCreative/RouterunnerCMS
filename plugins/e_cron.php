<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.08.30.
 * Time: 21:32
 */

session_start();

require 'config.php';

require $rootpath . 'runner-config.php';
require $rootpath . $runner_config["BACKEND_DIR"] . '/Routerunner/Routerunner.php';
use \Routerunner\Routerunner as runner;

new runner(array(
	'mode' => 'mail',
	'silent' => true,
	'method' => 'any',
	'resource' => '/',
	'bootstrap' => false,
), function() {

	$domain_limits = array(
		'@gmail.com' => 150,
		'@hotmail.com' => 150,
		'@t-online.hu' => 150,
	);
	$domain_sent = array(
		'@gmail.com' => 0,
		'@hotmail.com' => 0,
		'@t-online.hu' => 0,
	);
	$sent_email = 0;
	$sent_addresses = array();


	//if ((intval(date("G")) >= 9 && intval(date("G")) <= 20) || (isset($_GET["force"]) && $_GET["force"] == "1")) {

		$SQL = <<<SQL
SELECT cr.id AS cron_id, ca.id AS campaign_id, cr.test_address, cr.limit_per_period, cr.period, cr.start,
ca.category, ca.subject, ca.mail_html, ca.mail_text
FROM e_cron as cr
LEFT JOIN e_campaign AS ca ON ca.id = cr.campaign
WHERE ca.active = 1 AND COALESCE(cr.start, :time-1) < :time AND cr.finish IS NULL

SQL;

		$SQL_deliver = "INSERT INTO e_delivered (`cron_id`, `address_id`, `date`, `uhash`) VALUES (:cron, :address, :date, :hash)";

		if ($result = \db::query($SQL, array(":time" => time()))) {
			$cron = $result[0];

			$close_cron = false;

			$addresses = array();
			$SQL_addresses = false;

			$limit = null;
			if (!is_null($cron["period"]) && is_numeric($cron["period"])
				&& !is_null($cron["limit_per_period"]) && is_numeric($cron["limit_per_period"])
			) {
				$SQL_delivered_in_period = <<<SQL
SELECT COUNT(d.id) AS delivered_count FROM e_delivered AS d WHERE d.date BETWEEN :time_from AND :time_now

SQL;
				$limit = $cron["limit_per_period"];
				$params_delivered_in_period = array(
					":time_from" => time() - $cron["period"],
					":time_now" => time()
				);
				if ($result_delivered_in_period = \db::query($SQL_delivered_in_period, $params_delivered_in_period)) {
					$limit = $limit - $result_delivered_in_period[0]["delivered_count"];
				}
				if ($limit <= 0) {
					$limit = false;
				}
			}
			if ($limit !== false) {
				/*
				$SQL_request = 'INSERT INTO `request` (`date`, `url`, `referer`, `bootstrap`, `ip`, `useragent`, `cookie`, `server`) VALUES (:date, :url, :referer, :bs, :ip, :ua, :cookie, :server)';
				$params_request = array(
					":date" => time(),
					":url" => 'cron_start',
					":referer" => ((isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : ((isset($_SERVER["HTTP_FROM"])) ? $_SERVER["HTTP_FROM"] : null)),
					":bs" => $limit,
					":ip" => '',
					":ua" => '',
					":cookie" => '',
					":server" => '',
				);
				\db::insert($SQL_request, $params_request);
				*/


				if (!is_null($cron["test_address"]) && $cron["test_address"]) {
					$test_addresses = array();
					if (strpos($cron["test_address"], ",") !== false) {
						$test_addresses = array_merge($test_addresses, explode(",", $cron["test_address"]));
					}
					if (strpos($cron["test_address"], ";") !== false) {
						$test_addresses = array_merge($test_addresses, explode(";", $cron["test_address"]));
					}
					if (strpos($cron["test_address"], ",") === false && strpos($cron["test_address"], ";") === false) {
						$test_addresses[] = $cron["test_address"];
					}
					foreach ($test_addresses as $index => $test_address) {
						$addresses[$test_address] = array(
							"label" => "test " . $index,
							"name" => "test " . $index,
							"email" => $test_address,
							"link" => "http://test.com/" . $index,
							"category" => "test " . $index,
							"subscribe_date" => strftime("%Y-%m-%d %H:%M:%S", time()),
						);
					}
					$test_emails = "'" . implode("','", array_keys($addresses)) . "'";
					$SQL_test_addresses = <<<SQL
SELECT s.id, s.label, s.label AS name, s.email, s.link, s.category, FROM_UNIXTIME(s.date) AS subscribe_date
FROM e_subscriber AS s WHERE
s.email IN ({$test_emails})
ORDER BY s.date

SQL;
					if ($result_test_addresses = \db::query($SQL_test_addresses)) {
						foreach ($result_test_addresses as $result_test_address) {
							if (isset($addresses[$result_test_address['email']])) {
								$addresses[$test_address] = $result_test_address;
							}
						}
					}
					$close_cron = true;
				} elseif (!is_null($cron["category"]) && $cron["category"]) {
					$categories = false;
					if (strpos($cron["category"], ",") !== false) {
						$categories = explode(",", $cron["category"]);
					} elseif (strpos($cron["category"], ";") !== false) {
						$categories = explode(";", $cron["category"]);
					} elseif ($cron["category"] && strpos($cron["category"], ",") === false
						&& strpos($cron["category"], ";") === false
					) {
						$categories = array($cron["category"]);
					}
					$SQL_addresses = <<<SQL
SELECT s.id, s.label, s.label AS name, s.email, s.link, s.category, FROM_UNIXTIME(s.date) AS subscribe_date
FROM e_subscriber AS s WHERE
s.email NOT IN (SELECT email FROM e_subscriber WHERE unsubscribe IS NOT NULL)
AND s.id NOT IN (SELECT d.address_id FROM e_delivered AS d WHERE d.cron_id IN (SELECT id FROM `e_cron` WHERE campaign = :campaign))

SQL;
					if ($categories) {
						foreach ($categories as & $category) {
							$category = "'" . $category . "'";
						}
						$SQL_addresses .= "AND COALESCE(s.temp_category, s.category) IN (" . implode(", ", $categories) . ")" . PHP_EOL;
					}
					$SQL_addresses .= "ORDER BY s.date" . PHP_EOL;
				} else {
					$SQL_addresses = <<<SQL
SELECT s.id, s.label, s.label AS name, s.email, s.link, s.category, FROM_UNIXTIME(s.date) AS subscribe_date
FROM e_subscriber AS s WHERE
s.email NOT IN (SELECT email FROM e_subscriber WHERE unsubscribe IS NOT NULL)
AND s.id NOT IN (SELECT d.address_id FROM e_delivered AS d WHERE
	d.cron_id IN (SELECT id FROM `e_cron` WHERE campaign = :campaign))
ORDER BY s.date

SQL;
				}
				if ($SQL_addresses && ($result_addresses = \db::query($SQL_addresses, array(":campaign" => $cron["campaign_id"])))) {
					$addresses = array_merge($addresses, $result_addresses);
				}

				//var_dump(count($addresses), $addresses);
				//die();

				if ($addresses) {

					$header = array(
						"From" => \runner::config('Mail.From'),
						"FromName" => \runner::config('Mail.FromName'),
						"Subject" => $cron["subject"],
					);

					$dom = \phpQuery::newDocumentHTML($cron["mail_html"]);
					$mail_text = $cron["mail_text"];

					$nodes = pq("a");
					foreach ($nodes as $node) {
						if (substr(pq($node)->attr("href"), 0, 1) != "#" &&
							//substr(pq($node)->attr("href"), 0, 5) != "https" &&
							!pq($node)->hasClass("unsubscribe")) {
							pq($node)->attr("href", "[click]" . str_replace('%', '-percent-', urlencode(pq($node)->attr("href"))));
						}
					}
					if (count(pq("body")->elements)) {
						pq("body")->append("[open]");
						$mail_raw = $dom->html();
					} else {
						$mail_raw = $dom->html();
						$mail_raw .= "[open]";
					}

					foreach ($addresses as $address) {
						if ($sent_email <= $limit && isset($address["email"])
							&& preg_match("~^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$~i", trim($address["email"]))
						) {
							$address["email"] = trim($address["email"]);
							if (empty($address['name'])) {
								$address['name'] = substr($address['email'], 0, strpos($address['email'], '@'));
							}

							$domain = substr($address["email"], strpos($address["email"], "@"));
							$send_ok = true;
							if (isset($domain_limits[$domain])) {
								$domain_sent[$domain]++;
								if ($domain_sent[$domain] > $domain_limits[$domain]) {
									$send_ok = false;
								}
							}

							if ($send_ok) {
								// prepare mail
								$unique = uniqid();
								$hash = str_replace('/', ',', base64_encode(\Routerunner\Crypt::crypter($unique)));
								if (isset($address["id"])) {
									$params_deliver = array(
										":cron" => $cron["cron_id"],
										":address" => $address["id"],
										":date" => time(),
										":hash" => $unique,
									);
									$delivered = \db::insert($SQL_deliver, $params_deliver);
								} else {
									$delivered = 0;
								}

								/*
								$address["open"] = "";
								$address["click"] = \runner::config("BASE");
								$address["unsubscribe"] = \runner::config("BASE") . "unsubscribe/";
								*/

								$address["open"] = "<img alt='" . \runner::config("SITE") . "' src='" . \runner::config("BASE") . "nl/open/" . dechex($delivered) .
									"/" . $hash . "/" . "' style='display: none; width: 0; height: 0;'/>";
								$address["click"] = \runner::config("BASE") . "nl/click/" . dechex($delivered) . "/" . $hash . "/";
								$address["unsubscribe"] = \runner::config("BASE") . "nl/unsubscribe/" . dechex($delivered) . "/" .
									$hash . "/";
								

								$mail_content = urldecode($mail_raw);
								$mail_content_text = $mail_text;
								foreach ($address as $var => $value) {
									$mail_content = str_replace('[' . $var . ']', $value, $mail_content);
									$mail_content_text = str_replace('[' . $var . ']', $value, $mail_content_text);
								}


								// send mail
								\Routerunner\Mail::sender($address["email"], $header, $mail_content, null, $mail_content_text);
								$sent_email++;
								$sent_addresses[] = $address["email"];
							}
						} elseif (!(isset($address["email"])
							&& preg_match("~^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$~i", trim($address["email"])))) {
							$SQL_delete_address = 'UPDATE e_subscriber SET unsubscribe = 1 WHERE id = :id';
							\db::query($SQL_delete_address, array(':id' => $address["id"]));
						}
					}

					if ($SQL_addresses) {
						if (!\db::query($SQL_addresses, array(":campaign" => $cron["campaign_id"]))) {
							$close_cron = true;
						}
					}

					if ($close_cron) {
						$SQL = "UPDATE e_cron SET finish = :time WHERE id = :cron";
						\db::query($SQL, array(":time" => time(), ":cron" => $cron["cron_id"]));
					}
				}
			}
		}
		/*
		$SQL_request = 'INSERT INTO `request` (`date`, `url`, `referer`, `bootstrap`, `ip`, `useragent`, `cookie`, `server`) VALUES (:date, :url, :referer, :bs, :ip, :ua, :cookie, :server)';
		$params_request = array(
			":date" => time(),
			":url" => 'cron_end',
			":referer" => ((isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : ((isset($_SERVER["HTTP_FROM"])) ? $_SERVER["HTTP_FROM"] : null)),
			":bs" => print_r($sent_addresses, true),
			":ip" => '',
			":ua" => '',
			":cookie" => $sent_email,
			":server" => '',
		);
		\db::insert($SQL_request, $params_request);
		*/
	//}

});
