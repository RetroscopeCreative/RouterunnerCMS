<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.10.30.
 * Time: 20:47
 */
session_start();

require $_SESSION["routerunner-config"]['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $_SESSION["routerunner-config"]["SITEROOT"] . $_SESSION["routerunner-config"]["BACKEND_ROOT"] . 'Routerunner/Routerunner.php';
use \Routerunner\Routerunner as runner;

$post = array_merge($_GET, $_POST);

new runner(array(
	'mode' => 'backend',
	'params' => $post,
	'silent' => true,
	'method' => 'any',
	'resource' => '/',
	'bootstrap' => false,
), function() use ($post) {
	$response = array(
		"script" => false,
		"route" => false,
		"content" => false,
	);

	if (isset($post['url']) && is_array($post["url"])) {
		$urls = $post['url'];
	} else {
		$urls = array($post['url']);
	}
	foreach ($urls as $url) {
		$file = false;
		$class = false;
		$pathroot = (!is_array($url) ? $url : "");
		if (!$pathroot) {
			$pathroot = (isset($url["path"]) ? $url["path"] : "");
			if (isset($url["file"])) {
				if (is_array($url["file"])) {
					$file = $url["file"];
				} else {
					$file = array($url["file"]);
				}
			}
			if (isset($url["class"])) {
				$class = $url["class"];
			}
		}
		$dirsep = DIRECTORY_SEPARATOR;
		$crossdomain = false;
		if (preg_match("/^http(s)?:\/\//", $pathroot) || preg_match("/^\//", $pathroot)) {
			$crossdomain = true;
			$path = $pathroot;
		} else {
			$path = rtrim(\runner::config("SITEROOT") . $pathroot, $dirsep) . ($class ? $dirsep . $class : "");
		}
		if ($crossdomain) {
			$file_url = $path;
			if ($class) {
				$file_url = rtrim($file_url, $dirsep) . $dirsep . $class . $dirsep;
			}
			$found = false;
			if ($file) {
				foreach ($file as $current_file) {
					$file_url .= rtrim($file_url, $dirsep) . $dirsep . $current_file;
					$file_headers = @get_headers($file_url);
					if (!$found && isset($file_headers[0]) && strpos($file_headers[0], '404') !== false) {
						$response["content"] = file_get_contents($file_url);
					}
				}
			} else {
				$file_headers = @get_headers($file_url);
				if (isset($file_headers[0]) && strpos($file_headers[0], '404') !== false) {
					$response["content"] = file_get_contents($file_url);
				}
			}
		} else {
			if (!$file) {
				$file = array(substr($path, strrpos($path, $dirsep) + 1));
				$path = substr($path, 0, strrpos($path, $dirsep));
			}
			$checkfiles = $file;
			foreach ($checkfiles as $check_key => & $check_file) {
				$check_file = (strpos($check_file, "?") !== false
					? substr($check_file, 0, strpos($check_file, "?")) : $check_file);
			}

			$found = false;
			while (!$found && $path) {
				if (strpos($path, $dirsep) === false) {
					$path = false;
				} else {
					$path = substr($path, 0, strrpos($path, $dirsep));
				}
				if (is_dir($path)) {
					foreach ($file as $key => $current_file) {
						if (!$found && $path && $current_file && file_exists($path . $dirsep . $checkfiles[$key])) {
							$response["script"] = str_replace(\runner::config("SITEROOT"), "",
								$path . $dirsep . $checkfiles[$key]);
							$route = str_replace(\runner::config("SITEROOT"), "", $path);
							$response["route"] = explode($dirsep, $route);
							$response["content"] = file_get_contents($path . $dirsep . $checkfiles[$key]);
							$found = true;
						}
					}
				}
			}
			if (!$found) {
				$path = \runner::config("SITEROOT") . \runner::config("BACKEND_ROOT") . "scaffold" . $dirsep . "backend"
					. $dirsep . "input" . $dirsep;
				if (is_dir($path)) {
					foreach ($file as $key => $current_file) {
						if (!$found && $path && $current_file && file_exists($path . $dirsep . $checkfiles[$key])) {
							$response["script"] = str_replace(\runner::config("SITEROOT"), "",
								$path . $dirsep . $checkfiles[$key]);
							$route = str_replace(\runner::config("SITEROOT"), "", $path);
							$response["route"] = explode($dirsep, $route);
							$response["content"] = file_get_contents($path . $dirsep . $checkfiles[$key]);
							$found = true;
						}
					}
				}
			}
		}
	}
	echo json_encode($response);
});
