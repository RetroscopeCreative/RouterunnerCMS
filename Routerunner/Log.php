<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.12.05.
 * Time: 13:23
 */

namespace Routerunner;

/*
 * debug( mixed $object, array $context )
 * info( mixed $object, array $context )
 * notice( mixed $object, array $context )
 * warning( mixed $object, array $context )
 * error( mixed $object, array $context )
 * critical( mixed $object, array $context )
 * alert( mixed $object, array $context )
 * emergency( mixed $object, array $context )
 * log( mixed $level, mixed $object, array $context )
 */

class Log
{
	const EMERGENCY = 1;
	const ALERT     = 2;
	const CRITICAL  = 3;
	const FATAL     = 3; //DEPRECATED replace with CRITICAL
	const ERROR     = 4;
	const WARN      = 5;
	const NOTICE    = 6;
	const INFO      = 7;
	const DEBUG     = 8;

	public static $levels = array();

	public function __construct()
	{
	    self::$levels = array(
            self::EMERGENCY => 'EMERGENCY',
            self::ALERT     => 'ALERT',
            self::CRITICAL  => 'CRITICAL',
            self::ERROR     => 'ERROR',
            self::WARN      => 'WARNING',
            self::NOTICE    => 'NOTICE',
            self::INFO      => 'INFO',
            self::DEBUG     => 'DEBUG'
        );

	}

	public function write($message, $level = null)
	{
		$backtrace = false;
		try {
			$backtrace = debug_backtrace();
		} catch (\Exception $e) { }
		$backtrace = '';

		$skip_shift = false;
		if ($level === 1 && strpos($message, PHP_EOL) !== false) {
			$backtrace = explode(PHP_EOL, $message);
			$message = array_shift($backtrace);
			$skip_shift = true;
		}
		if (is_array($message)) {
			$message = json_encode($message, JSON_PRETTY_PRINT);
		}

		$log = array(
			':date' => time(),
			':exception' => 'EXCEPTION',
			':message' => $message,
			':file' => '',
			':line' => '',
			':trace' => array(),
		);
		$log[':exception'] = self::$levels[$level];

		if (is_array($backtrace) && ($skip_shift || count($backtrace) > 2)) {
            if (!$skip_shift) {
                array_shift($backtrace);
                array_shift($backtrace);
                $current = array_shift($backtrace);
                $log[':file'] = $current['file'];
                $log[':line'] = $current['line'];
            } elseif (($in_pos = strrpos($message, ' in ')) !== false) {
                $log[':file'] = substr($message, $in_pos + 4, strpos($message, ':', $in_pos + 4) - ($in_pos + 4));
                $log[':line'] = substr($message, strpos($message, ':', $in_pos + 4) + 1);
            }
        }
        /*
        if (isset($current['function'])) {
            $log['exception'] = strtoupper($current['function']);
        }
        */
        if (!is_null($level)) {
            $log[':exception'] .= ' (level=' . $level . ')';
        }
        if ($backtrace) {
            foreach ($backtrace as $item) {
                if (isset($item['object'])) {
                    $item['object'] = get_class($item['object']);
                }
                $log[':trace'][] = $item;
            }
            $log[':trace'] = json_encode($log[':trace'], JSON_PRETTY_PRINT);
        }
        if (empty($log[':trace'])) {
            $log[':trace'] = '';
        }
        if ($uid = \Routerunner\User::me()) {
            $log[':trace'] .= "<br /><br /><br />USER: {$uid}<br /><br />";
        }
        if (!empty($_GET)) {
            $log[':trace'] .= "<br /><br /><br />GET:<br />" . json_encode($_GET, JSON_PRETTY_PRINT);
        }
        if (!empty($_POST)) {
            $log[':trace'] .= "<br /><br /><br />POST:<br />" . json_encode($_POST, JSON_PRETTY_PRINT);
        }
        /*
        if (!empty($_SESSION)) {
            $log[':trace'] .= "<br /><br /><br />SESSION:<br />" . json_encode($_SESSION, JSON_PRETTY_PRINT);
        }
        */
        if (!empty($_SERVER)) {
            $log[':trace'] .= "<br /><br /><br />SERVER:<br />" . json_encode($_SERVER, JSON_PRETTY_PRINT);
        }

        $SQL = <<<SQL
INSERT INTO `{PREFIX}log` (`date`, `exception`, `message`, `file`, `line`, `trace`, `solved`)
VALUES (:date, :exception, :message, :file, :line, :trace, 0)

SQL;
        if (empty($log[':line']) || !is_numeric($log[':line'])) {
            $log[':line'] = 0;
        }
        $mail = $log;
        if (strlen($log[':message']) > 255) {
            $log[':message'] = substr($log[':message'], 0, 252) . '...';
        }
        \db::insert($SQL, $log);

        if (\runner::config('log.email')) {
            \mail::mailer('/mail/error', $mail);
        }

        if (\runner::config('log.enabled') && $level < 5) {
            $traced = json_encode($backtrace, JSON_PRETTY_PRINT);

            echo <<<HTML
<h1>{$log[':exception']} raised!</h1>
<h3>Message: {$message}</h3>
<h5>File: {$log[':file']}:{$log[':line']}</h5>
<h5>Date: {$log[':date']}</h5>
{$traced}

HTML;
            die();
        }
	}
}
