<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.12.03.
 * Time: 18:07
 */

namespace Routerunner;

class Db
{
	private static $db_conn = false;
	private static $db_transaction = false; // stores if transaction is opened

	private static $host = false;
	private static $port = false;
	private static $user = false;
	private static $pwd = false;
	private static $db = false;
	private static $provider = false;
	private static $charset = false;

	private static $db_prefix = false;
	private static $prefix = false;

	private static $inited = false;
	private static $log = false;
	private static $detail = false;

	public static function initialize($settings)
	{
		if (isset($settings['DB_DEBUG']) && $settings['DB_DEBUG']) {
			self::$inited = microtime(true);
			self::$log = fopen('log-' . self::$inited . '.log', 'a+');
			self::$detail = fopen('detail-' . self::$inited . '.log', 'a+');
		}

		self::$host = $settings['DB_HOST'];
		self::$port = (isset($settings['DB_PORT'])) ? $settings['DB_PORT'] : '3306';
		self::$user = $settings['DB_USER'];
		self::$pwd = $settings['DB_PASS'];
		self::$db = $settings['DB_NAME'];
		self::$provider = (isset($settings['PROVIDER'])) ? $settings['PROVIDER'] : 'mysql';
		self::$charset = (isset($settings['DB_CHARSET'])) ? $settings['DB_CHARSET'] : 'utf8';

		self::$db_prefix = (isset($settings['DB_PREFIX'])) ? $settings['DB_PREFIX'] : '';
		self::$prefix = (isset($settings['PREFIX'])) ? $settings['PREFIX'] : '';

		if (!defined("DB_MERGE_FIRSTCOL"))
			define("DB_MERGE_FIRSTCOL", 1);
		if (!defined("DB_MERGE_SECONDCOL"))
			define("DB_MERGE_SECONDCOL", 2);
		if (!defined("DB_MERGE_THIRDCOL"))
			define("DB_MERGE_THIRDCOL", 3);
		if (!defined("DB_FIRSTROW"))
			define("DB_FIRSTROW", 4);

		self::db_connect();
	}

	private static function db_connect($force=false)
	{
		if (!self::$db_conn || $force) {
			switch (strtolower(self::$provider)) {
				case "mysql":
					try {
						$db_params = array(1002 => "SET NAMES ".self::$charset, \PDO::ATTR_EMULATE_PREPARES => 1);
						$db_connect = new \PDO("mysql:host=".self::$host.";port=".self::$port.";dbname=".self::$db,
							self::$user, self::$pwd, $db_params);
						$db_connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
						self::$db_conn = $db_connect; // store database connection
						break;
					} catch (\PDOException $err) {
						var_dump($err);
						$debug = true;
					}
			}
		}
	}

	/*
	 * [helper] static [begin_transaction] function
	 * --------------------------------------------
	 * begins a transaction
	 */
	public static function begin_transaction() {
		if (self::$db_transaction) {
			return false;
		} else {
			self::$db_transaction = self::$db_conn->beginTransaction();
			return self::$db_transaction;
		}
	}

	/*
	 * [helper] static [commit] function
	 * ---------------------------------
	 * commits the transaction if opened
	 */
	public static function commit() {
		if (self::$db_transaction) {
			self::$db_conn->commit();
			self::$db_transaction = false;
		}
	}

	/*
	 * [helper] static [rollback] function
	 * -----------------------------------
	 * rollbacks the transaction if opened
	 */
	public static function rollback() {
		if (self::$db_transaction) {
			self::$db_conn->rollback();
			self::$db_transaction = false;
		}
	}

	/*
	 * [helper] static master [query] function
	 * ---------------------------------------
	 * $SQL: the SQL string for process
	 * $params: the PDO param array; keys are the filters, values are the conditions
	 * -----------------------------------------------------------------------------
	 * runs the query & returns the result array
	 */
	public static function query($SQL=null, $params=array(), $flags=0) {
		$start = microtime(true) - self::$inited;

		$SQL = str_replace("{PREFIX}", self::$db_prefix, $SQL);
		$SQL = str_replace("{PFX}", self::$prefix, $SQL);

		if (\Routerunner\Common::isAssoc($params)) {
			$parameters = array();
			// bind valid params
			foreach ($params as $bind => $param) {
				if (strpos($SQL, $bind) !== false) {
					$parameters[$bind] = $param;
				}
			}
		} else {
			$parameters = $params;
		}

		$return = array(); // return empty array if execution fails
		//try {
			$stmt = self::$db_conn->prepare($SQL);
			if ($stmt->execute($parameters)) {
				// execute & fetch the data
				$stmt->setFetchMode(\PDO::FETCH_ASSOC);
				$return = $stmt->fetchAll();
			}
		//} catch (\PDOException $err) {
		//	var_dump($SQL, $parameters);
		//	$debug = true;
		//}

		if (count($return)) {
			if ($flags & DB_MERGE_FIRSTCOL || $flags & DB_MERGE_SECONDCOL || $flags & DB_MERGE_THIRDCOL) {
				$merge_return = array();
				foreach ($return as $row) {
					if ($flags & DB_MERGE_FIRSTCOL)
						$merge_return[] = array_slice($row, 0, 1);
					if ($flags & DB_MERGE_SECONDCOL)
						$merge_return[] = array_slice($row, 1, 1);
					if ($flags & DB_MERGE_THIRDCOL)
						$merge_return[] = array_slice($row, 2, 1);
				}
				$return = $merge_return;
			}
			if ($flags & DB_FIRSTROW) {
				$return = array_shift($return);
			}
		} else {
			$return = false;
		}
		$end = microtime(true) - self::$inited;

		if (self::$log) {
			fwrite(self::$log, $start . "\t" . $end . "\t" . $SQL . PHP_EOL);
			fwrite(self::$detail, $start . "\t" . $end . PHP_EOL . "SQL:" . $SQL . PHP_EOL . "params:" . print_r($params, true) . PHP_EOL . "return:" . print_r($return, true) . PHP_EOL);
		}

		return $return;
	}

	/*
	 * [helper] static master [insert] function
	 * ----------------------------------------
	 * $SQL: the SQL string for process
	 * $params: the PDO param array
	 * ----------------------------
	 * runs the query & returns the last inserted id
	 */
	public static function insert($SQL=null, $params=array()) {
		$start = microtime(true) - self::$inited;

		$SQL = str_replace("{PREFIX}", self::$db_prefix, $SQL);
		$SQL = str_replace("{PFX}", self::$prefix, $SQL);
		$stmt = self::$db_conn->prepare($SQL);

		if (\Routerunner\Common::isAssoc($params)) {
			$parameters = array();
			// bind valid params
			foreach ($params as $bind => $param) {
				if (strpos($SQL, $bind) !== false) {
					$parameters[$bind] = $param;
				}
			}
		} else {
			$parameters = $params;
		}
		$return = null; // return null if execution fails
		if ($stmt->execute($parameters)) {
			// execute & fetch the last inserted id
			$return = self::$db_conn->lastInsertId();
			if ($return == false) {
				$return = true;
			}
		} else {
			//exc::soft(null, $stmt->errorInfo(), array("SQL"=>$SQL, "params"=>$params));
		}
		$end = microtime(true) - self::$inited;

		if (self::$log) {
			fwrite(self::$log, $start . "\t" . $end . "\t" . $SQL . PHP_EOL);
			fwrite(self::$detail, $start . "\t" . $end . PHP_EOL . "SQL:" . $SQL . PHP_EOL . "params:" . print_r($params, true) . PHP_EOL . "return:" . print_r($return, true) . PHP_EOL);
		}

		return $return;
	}

	public static function escape($str) {
		return self::$db_conn->quote($str);
	}
}