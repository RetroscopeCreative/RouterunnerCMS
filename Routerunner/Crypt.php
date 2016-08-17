<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.01.16.
 * Time: 14:39
 */
namespace Routerunner;

class Crypt
{
	private static $method = '';//'CRYPT_BLOWFISH';
	private static $salt = 'RouterunnerDemo_Salt';
	private static $logarithm = '07';

	public static function crypter($input, $keep=null, $store=null, $renew=0,
								 $unique_salt=null, $unique_logarithm=null, $unique_method=null)
	{
		$_method = ((!is_null($unique_method)) ? $unique_method : self::$method);
		$_salt = ((!is_null($unique_salt)) ? $unique_salt : self::$salt);
		switch ($_method) {
			case 'CRYPT_BLOWFISH':
				$_logarithm = ((!is_null($unique_logarithm)) ? $unique_logarithm : self::$logarithm);
				$salt = '$2a$'.$_logarithm.'$'.$_salt.'$';
				break;
			default:
				$salt = $_salt;
		}

		if (is_array($input)) {
			$input = implode('', $input);
		}
		if (!$_method) {
			$hash = md5($salt . $input);
		} else {
			$hash = crypt($input, $salt);
		}

		$return = $hash;

		if (!is_null($keep)) {
			$secret = str_replace('.', '', uniqid('', true));

			$SQL = 'INSERT INTO {PREFIX}crypt (hash, crypted, secret, base, reference, keep, renew)';
			$SQL .= 'VALUES (:hash, :crypted, :secret, :base, :reference, :keep, :renew)';
			$params = array(
				':hash'=>$hash,
				':crypted'=>$input,
				':secret'=>$secret,
				':base'=>null,
				':reference'=>null,
				':keep'=>$keep,
				':renew'=>$renew
			);
			if (isset($store) && is_numeric($store)) {
				$params[':reference'] = $store;
			} elseif (isset($store) && is_array($store)) {
				$params[':base'] = json_encode($store);
			} elseif (isset($store)) {
				$params[':base'] = $store;
			}
			\Db::insert($SQL, $params);

			$return = $secret;
		}
		return $return;
	}

	public static function checker($input, $check,
								 $unique_salt=null, $unique_logarithm=null, $unique_method=null)
	{
		$return = false;
		$hash = self::crypter($input, null, null, 0, $unique_salt, $unique_logarithm, $unique_method);
		if ($hash === $check) {
			$return = true;
		}
		return $return;
	}

	public static function checker_secret($input, $secret, & $stored=null,
								 $unique_salt=null, $unique_logarithm=null, $unique_method=null)
	{
		$salt = self::get_crypter($unique_salt, $unique_logarithm, $unique_method);
		$hash = crypt($input, $salt);

		$SQL = <<<SQL
SELECT crypt_id, keep, renew FROM {PREFIX}crypt
WHERE hash = :hash AND secret = :secret AND keep > UNIX_TIMESTAMP()
SQL;
		$params = array(
			':hash'=>$hash,
			':secret'=>$secret
		);
		return self::return_crypter($SQL, $params, $hash, $stored);
	}

	public static function checker_reference($input, $reference, & $stored=null,
								 $unique_salt=null, $unique_logarithm=null, $unique_method=null)
	{
		$salt = self::get_crypter($unique_salt, $unique_logarithm, $unique_method);
		$hash = crypt($input, $salt);

		$SQL = 'SELECT crypt_id, keep, renew FROM {PREFIX}crypt WHERE hash = :hash AND reference = :reference';
		$params = array(
			':hash'=>$hash,
			':reference'=>$reference
		);
		return self::return_crypter($SQL, $params, $hash, $stored);
	}

	private static function get_crypter($unique_salt=null, $unique_logarithm=null, $unique_method=null)
	{
		$_method = ((!is_null($unique_method)) ? $unique_method : self::$method);
		$_salt = ((!is_null($unique_salt)) ? $unique_salt : self::$salt);
		switch ($_method) {
			case 'CRYPT_BLOWFISH':
				$_logarithm = ((!is_null($unique_logarithm)) ? $unique_logarithm : self::$logarithm);
				$salt = '$2a$'.$_logarithm.'$'.$_salt.'$';
				break;
			default:
				$salt = $_salt;
		}
		return $salt;
	}

	private static function return_crypter($SQL, $params, $hash, & $stored=null)
	{
		$return = false;
		if ($result = \Db::query($SQL, $params)) {
			$stored = self::decrypter($result[0]['crypt_id'], $hash);
			if (!is_null($result[0]['keep'])) {
				$params = array(':id'=>$result[0]['crypt_id']);
				if ($result[0]['renew'] === "0") {
					$SQL = 'DELETE FROM {PREFIX}crypt WHERE crypt_id = :id';
					$SQL = 'DELETE FROM {PREFIX}crypt WHERE crypt_id = :id AND 1 = 0';
				} elseif ($result[0]['keep'] > time() && is_numeric($result[0]['renew'])) {
					$SQL = 'UPDATE {PREFIX}crypt SET keep = :keep WHERE crypt_id = :id';
					$params[':keep'] = time() + (int) $result[0]['renew'];
				}
				\Db::query($SQL, $params);
			}
			$return = true;
		}
		return $return;
	}

	public static function decrypter($id, $hash)
	{
		$return = null;
		$SQL = 'SELECT reference, base FROM {PREFIX}crypt WHERE crypt_id = :id AND hash = :hash';
		$params = array(
			':id'=>$id,
			':hash'=>$hash
		);
		if ($result = \Db::query($SQL, $params)) {
			if (isset($result[0]['reference']) && is_numeric($result[0]['reference'])) {
				$return = $result[0]['reference'];
			} elseif (isset($result[0]['base']) && !is_null($json = json_decode($result[0]['base'], true))) {
				$return = $json;
			} elseif (isset($result[0]['base'])) {
				$return = $result[0]['base'];
			}
		}
		return $return;
	}

	public static function delete_crypt($hash, $crypted=null, $secret=null, $stored=null, $keep=null)
	{
		$SQL = 'SELECT crypt_id FROM {PREFIX}crypt WHERE hash = :hash';
		$params = array(':hash' => $hash);
		if (isset($crypted)) {
			$SQL .= ' AND crypted = :crypted';
			$params[':crypted'] = $crypted;
		}
		if (isset($secret)) {
			$SQL .= ' AND secret = :secret';
			$params[':secret'] = $secret;
		}
		if (isset($stored)) {
			if (is_numeric($stored)) {
				$SQL .= ' AND reference = :reference';
				$params[':reference'] = $stored;
			} else {
				$SQL .= ' AND base = :base';
				if (is_array($stored)) {
					$params[':base'] = json_encode($stored);
				} else {
					$params[':base'] = $stored;
				}
			}
		}
		if (isset($keep)) {
			$SQL .= ' AND keep = :keep';
			$params[':keep'] = $keep;
		}
		if ($result = db::query($SQL, $params)) {
			$crypt_id = $result[0]['crypt_id'];
			$SQL = str_replace('SELECT crypt_id', 'DELETE', $SQL) . ' AND crypt_id = :crypt_id';
			$params[':crypt_id'] = $crypt_id;

			db::query($SQL, $params);
		}
	}

	private static function strtoint($str)
	{
		$return = '';
		while (($chr = substr($str, 0, 1)) !== false) {
			$return .= ord($chr);
			$str = substr($str, 1);
		}
		return $return;
	}
}