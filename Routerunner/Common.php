<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.12.03.
 * Time: 18:43
 */

namespace Routerunner;

class Common {
	public static function isAssoc($arr)
	{
		return array_keys($arr) !== range(0, count($arr) - 1);
	}

	public static function dbField($input, $prefix='`', $suffix='`', $exclude='.', $trim=false, $cut=false)
	{
		$input = trim($input, $trim);
		if ($cut) {
			$input = trim(substr($input, strrpos($input, $cut)), $cut);
		}
		if (substr($input, 0, strlen($prefix)) !== $prefix && strpos($input, $exclude) === false)
			$input = $prefix . $input;
		if (substr($input, -1*strlen($suffix)) !== $suffix && strpos($input, $exclude) === false)
			$input .= $suffix;
		return $input;
	}

	public static function arrDiff($arg1, $arg2=array())
	{
		$diff = array();
		$keys = array_merge(array_keys($arg1), array_keys($arg2));
		foreach ($keys as $key)
		{
			if (!isset($arg2[$key]) || $arg1[$key] !== $arg2[$key]) {
				$diff[$key] = $arg1[$key];
			}
		}
		return $diff;
	}

	public static function inc($path='', $file='', $root=true, $require=false, $once=false, $suffix='')
	{
		$return = false;
		$inc = realpath(\Routerunner\Helper::$scaffold_root . DIRECTORY_SEPARATOR . $suffix);
		if (substr($inc, -1) !== DIRECTORY_SEPARATOR)
			$inc .= DIRECTORY_SEPARATOR;
		if ($root) {
			$inc .= \Routerunner\Routerunner::$static->config("root");
			if (substr($inc, -1) !== DIRECTORY_SEPARATOR)
				$inc .= DIRECTORY_SEPARATOR;
		}
		$inc .= (substr($path, 0, 1) === DIRECTORY_SEPARATOR) ? substr($path, 1) : $path;
		if (substr($inc, -1) !== DIRECTORY_SEPARATOR)
			$inc .= DIRECTORY_SEPARATOR;
		$inc .= (substr($file, 0, 1) === DIRECTORY_SEPARATOR) ? substr($file, 1) : $file;
		if (substr($inc, -4) != '.php')
			$inc .= '.php';

		if (file_exists($inc))
		{
			if ($require) {
				if ($once) {
					$return = (require_once $inc);
				} else {
					$return = (require $inc);
				}
			} else {
				if ($once) {
					$return = (include_once $inc);
				} else {
					$return = (include $inc);
				}
			}
		}
		return $return;
	}
}