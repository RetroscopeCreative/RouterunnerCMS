<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.10.22.
 * Time: 22:00
 */



session_start();

require 'config.php';

require $rootpath . 'runner-config.php';
require $rootpath . 'RouterunnerCMS/Routerunner/Routerunner.php';
use \Routerunner\Routerunner as runner;

new runner(array(
	'mode' => 'mail',
	'silent' => true,
	'method' => 'any',
	'resource' => '/',
	'bootstrap' => false,
), function() {

	$mailboxPath = '';
	$username = '';
	$password = '';

	$pattern = '/\/nl\/open\/([a-f0-9]{4,})\//';

	$SQL_SELECT = "SELECT address_id FROM e_delivered WHERE id = :deliver";
	$SQL_UPDATE = "UPDATE e_subscriber SET unsubscribe = 2, bounced = :time WHERE id = :address_id";

	$imap = imap_open($mailboxPath, $username, $password);

	$numMessages = imap_num_msg($imap);
	$skip = 0;
	$bounced = 0;
	for ($i = $numMessages; $i > 0; $i--) {
		echo "no.$i<br>\n";
		$header = imap_header($imap, $i);

		if (isset($header->subject)) {
			$subject = strtolower(imap_utf8($header->subject));

			if ($subject == 'mail delivery failed: returning message to sender' ||
				strpos($subject, 'delivery status') !== false ||
				(strpos($subject, 'deliver') !== false && strpos($subject, 'fail') !== false) ||
				(strpos($subject, 'return') !== false && strpos($subject, 'mail') !== false) ||
				strpos($subject, 'undeliver') !== false ||
				strpos($subject, 'non remis') !== false ||
				strpos($subject, 'unknown address') !== false ||
				strpos($subject, 'onbestelbaar') !== false ||
				strpos($subject, 'unzustellbar') !== false ||
				strpos($subject, 'nedoru=e8iteln=e9') !== false ||
				strpos($subject, 'rejected') !== false ||
				(strpos($subject, 'failure') !== false && strpos($subject, 'notice') !== false) ||
				strpos($subject, 'k=e9zbes=edthetetlen') !== false ||
				strpos($subject, 'kézbesíthetetlen') !== false
			) {

				$uid = imap_uid($imap, $i);
				$body = imap_utf8(imap_body($imap, $i));

				if (preg_match($pattern, $body, $match)) {
					$open = $match[1];
					$deliver = hexdec($open);
					$params_select = array(":deliver" => $deliver);
					if ($result = \db::query($SQL_SELECT, $params_select)) {
						$address_id = $result[0]["address_id"];

						echo "fromaddress=" . $header->fromaddress . "<br>\n";
						echo "subject=" . $header->subject . "<br>\n";
						echo "customer=" . $address_id . "<br>\n";
						echo "<br>\n";
						$params_update = array(":address_id" => $address_id, ":time" => time());
						\db::query($SQL_UPDATE, $params_update);
					}
				}
				imap_delete($imap, $uid, FT_UID);
				imap_expunge($imap);
				$bounced++;
			} elseif (strpos($subject, 'out of office') !== false ||
				strpos($subject, 'automatic reply') !== false ||
				strpos($subject, 'automatikus') !== false ||
				strpos($subject, 'auto') !== false ||
				strpos($subject, 'automatic reply') !== false ||
				strpos($subject, 'delay') !== false ||
				strpos($subject, 'h=e1zon_k=edv=fcl') !== false ||
				strpos($subject, 'házon kívül') !== false
			) {

				$uid = imap_uid($imap, $i);
				imap_delete($imap, $uid, FT_UID);
				imap_expunge($imap);
			} else {
				$skip++;
			}
		}
	}
	imap_close($imap);
	//cron_run("bounced", $bounced);
});