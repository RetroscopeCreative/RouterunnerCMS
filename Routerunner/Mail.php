<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.01.17.
 * Time: 16:54
 */
namespace Routerunner;

class Mail
{
	public static function mailer($route, $context=array(), $attachment=null)
	{
		$return = false;
		$mail = \runner::return_route($route, $context, $router);
		$address = (isset($router->runner->context['address'])) ? $router->runner->context['address'] : false;
		$header = (isset($router->runner->context['header'])) ? $router->runner->context['header'] : array();
		if ($address || (isset($header['AddAddress']) && is_array($header['AddAddress']))) {
			$return = self::sender($address, $header, $mail, $attachment);
		}
		return $return;
	}

	public static function sender($address, $header=array(), $body, $attachment=null, $body_text=false)
	{
		$result = array();

		require_once 'PHPMailer' . DIRECTORY_SEPARATOR . 'class.phpmailer.php';

		$mail = new \PHPMailer;

		$mail->IsSMTP();                                             // Set mailer to use SMTP
		$mail->Host = \runner::config('Mail.Host');                  // Specify main and backup server
		$mail->SMTPAuth = \runner::config('Mail.SMTPAuth');          // Enable SMTP authentication
		$mail->Port = \runner::config('Mail.Port');
		$mail->Username = \runner::config('Mail.Username');          // SMTP username
		$mail->Password = \runner::config('Mail.Password');          // SMTP password
		$mail->SMTPSecure = \runner::config('Mail.SMTPSecure');      // Enable encryption, 'ssl' also accepted

		$mail->From = \runner::config('Mail.From');
		$mail->FromName = \runner::config('Mail.FromName');

		$mail->WordWrap = \runner::config('Mail.WordWrap');          // Set word wrap to 50 characters

		if (isset($attachment) && is_array($attachment) && count($attachment)) {
			//$mail->AddStringAttachment($string,$filename,$encoding,$type);
			if (isset($attachment['string'], $attachment['filename'], $attachment['encoding'], $attachment['type'])) {
				$mail->AddStringAttachment($attachment['string'], $attachment['filename'], $attachment['encoding'], $attachment['type']);
			} elseif (isset($attachment['string'], $attachment['filename'])) {
				$mail->AddStringAttachment($attachment['string'], $attachment['filename']);
			} else {
				foreach ($attachment as $item) {
					$mail->addAttachment($item);
				}
			}
		} elseif (isset($attachment)) {
			$mail->addAttachment($attachment);
		}

		$mail->IsHTML(true);                                         // Set email format to HTML
		$mail->CharSet = \runner::config('Mail.CharSet');

		$mail->Subject = \runner::config('Mail.Subject');

		foreach ($header as $mail_property=>$mail_value) {
			switch ($mail_property) {
				case "Host":
					$mail->Host = $mail_value;
					break;
				case "SMTPAuth":
					$mail->SMTPAuth = $mail_value;
					break;
				case "Port":
					$mail->Port = $mail_value;
					break;
				case "SMTPSecure":
					$mail->SMTPSecure = $mail_value;
					break;
				case "Username":
					$mail->Username = $mail_value;
					break;
				case "Password":
					$mail->Password = $mail_value;
					break;
				case "From":
					$mail->From = $mail_value;
					break;
				case "FromName":
					$mail->FromName = $mail_value;
					break;
				case "Subject":
					$mail->Subject = $mail_value;
					break;
				case "AddCC":
					if (is_array($mail_value)) {
						foreach ($mail_value as $cc) {
							if (isset($cc[0], $cc[1])) {
								$mail->AddCC($cc[0], $cc[1]);
							} else {
								$mail->AddCC($cc);
							}
						}
					} else {
						$mail->AddCC($mail_value);
					}
					break;
				case "AddReplyTo":
					if (is_array($mail_value)) {
						foreach ($mail_value as $reply) {
							if (isset($reply[0], $reply[1])) {
								$mail->AddReplyTo($reply[0], $reply[1]);
							} else {
								$mail->AddReplyTo($reply);
							}
						}
					} else {
						$mail->AddReplyTo($mail_value);
					}
					break;
				case "AddBCC":
					if (is_array($mail_value)) {
						foreach ($mail_value as $bcc) {
							if (isset($bcc[0], $bcc[1])) {
								$mail->AddBCC($bcc[0], $bcc[1]);
							} else {
								$mail->AddBCC($bcc);
							}
						}
					} else {
						$mail->AddBCC($mail_value);
					}
					break;
			}
		}
		if ($body_text && trim($body_text)) {
			$alt_body = $body_text;
		} else {
			$alt_from = array("</p>", "<br>", "<br/>", "<br />", "\r\n", "\n\n", "\t");
			$alt_to = array("\n", "\n", "\n", "\n", "\n", "\n", "");
			$alt_body = substr($body, strpos($body, "<body"), strpos($body, "</body>") - strpos($body, "<body"));
			$alt_body = strip_tags(str_replace($alt_from, $alt_to, $alt_body));
			$alt_body = $alt_body;
		}
		$mail->Body = $body;
		$mail->AltBody = $alt_body;

		if (isset($header["AddAddress"]) && is_array($header["AddAddress"])) {
			if ($address && !in_array($address, $header["AddAddress"])) {
				$header["AddAddress"][] = $address;
			}
			foreach ($header["AddAddress"] as $address) {
				$skip = false;
				$mail->ClearAddresses();
				if (is_array($address) && isset($address[0], $address[1])) {
					$mail->AddAddress($address[0], $address[1]);
					$address = $address[0];
				} elseif (isset($address)) {
					$mail->AddAddress($address);
				} else {
					$skip = true;
				}
				if (!$skip) {
					if(!$mail->Send()) {
						$result[$address] = false;
					} else {
						$result[$address] = true;
					}
				}
			}
		} elseif ($address) {
			$mail->ClearAddresses();
			$mail->AddAddress($address);
			if(!$mail->Send()) {
				$result[$address] = false;
			} else {
				$result[$address] = true;
			}
		}

		if (count($result) == 1) {
			$result = array_shift($result);
		}
		return $result;
	}
}