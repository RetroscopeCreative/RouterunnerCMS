<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.06.03.
 * Time: 11:26
 */
use PHPImageWorkshop\ImageWorkshop;

require '../../../Routerunner/Routerunner.php';
use \Routerunner\Routerunner as runner;

$post = array_merge($_GET, $_POST);

new runner(array(
	'mode' => 'backend',
	'params' => $post,
	'method' => 'post',
	'resource' => '/',
	'bootstrap' => false,
	'silent' => true,
), function() use ($post) {
	$response = array(
		"success" => false,
		"apply" => false,
		"change_id" => false,
		"error" => array(),
	);

	require_once($_SESSION["routerunner-config"]['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $_SESSION["routerunner-config"]["SITEROOT"] . $_SESSION["routerunner-config"]["BACKEND_ROOT"] . 'backend/thirdparty/ImageWorkshop/src' . '/PHPImageWorkshop/ImageWorkshop.php');
	foreach ($post["value"] as $field => $value) {
		$crops = array();

		$reference = $post["model"]["reference"];

		$_model_context = array(
			"direct" => $reference,
			"session" => (\runner::stack("session_id") ? \runner::stack("session_id") : null),
		);
		$router = false;
		$route = $post["model"]["route"];
		\runner::redirect_route($route, \runner::config("scaffold"), true, $_model_context, $router, $model);
		if (is_array($model) && count($model) == 1) {
			$model = array_shift($model);
		}
		if ($model) {
			$root_router = false;
			$root_model = false;

			$route = '/model/' . $model->class;
			\runner::redirect_route($route, \runner::config("scaffold"), true, $_model_context, $root_router, $root_model);

			if (isset($router->runner->backend_context["model"]["fields"])) {
				$fields = $router->runner->backend_context["model"]["fields"];
			}
			if (isset($root_router->runner->backend_context["model"]["fields"])) {
				$fields = array_merge($fields, $root_router->runner->backend_context["model"]["fields"]);
			}

			foreach ($fields as $field_name => $field_data) {
				if (isset($field_data["crop"])) {
					$crops[$field_name] = $field_data["crop"];
				}
			}
			$field_data = $fields[$field];
		}


		$parents = \Routerunner\Bootstrap::parent($reference);
		if (isset($parents[0]["model_class"]) && $parents[0]["model_class"] == "lang") {
			$lang = array_shift($parents);
		}

		$path_route = '';

		while ($parent = array_shift($parents)) {
			$_model_context = array(
				"direct" => $parent["reference"],
				"session" => \runner::stack("session_id"),
			);
			$router = false;
			$route = '/model/' . $parent["model_class"];
			\runner::redirect_route($route, \runner::config("scaffold"), true, $_model_context, $router, $parent_model);
			if (is_array($parent_model) && count($parent_model) == 1) {
				$parent_model = array_shift($parent_model);
			}
			if (isset($parent_model) && is_object($parent_model)
				&& get_parent_class($parent_model) == "Routerunner\\BaseModel" && isset($parent_model->label)) {
				$path_route .= \runner::toAscii($parent_model->label) . DIRECTORY_SEPARATOR;
			}

			$debug = 1;
		}


		if (isset($value["src"])) {
			// crop image
			$src = $_SESSION["routerunner-config"]['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .
				$_SESSION["routerunner-config"]["SITEROOT"] . $value["src"];
			$filename = substr($value["src"], strrpos($value["src"], DIRECTORY_SEPARATOR) + 1);

			$mimetype = false;
			$layer = ImageWorkshop::initFromPath($src, false, $mimetype);

			if (isset($value["rotate"])) {
				$layer->rotate($value["rotate"]);
			} elseif (isset($value["angle"])) {
				$layer->rotate($value["angle"]);
			}

			if ($crops) {


				$layers = array();
				unset($crops[$field]);
				foreach ($crops as $size => $crop_data) {
					$layers[$size] = clone $layer;

					$crop_field = str_replace("data_", "", $size);

					$crop_data["id"] = $model->table_id;
					$crop_data[$size] = array(
						"src" => $value["src"],
						"angle" => $value["angle"],
					);

					$width = (isset($crop_data["width"]) ? $crop_data["width"] : null);
					$height = (isset($crop_data["height"]) ? $crop_data["height"] : null);
					$anchor = (isset($crop_data["anchor"]) ? $crop_data["anchor"] : "LT");
					if ($width && $height) {
						if ($value["width"]) {
							$zoom_mod = $width / $value["width"];
						} elseif ($value["height"]) {
							$zoom_mod = $height / $value["height"];
						}
						$layers[$size]->resizeInPercent($value["zoom"] * $zoom_mod, $value["zoom"] * $zoom_mod, true);
						$layers[$size]->cropInPixel($width, $height,
							abs($value["x"]) * $zoom_mod, abs($value["y"]) * $zoom_mod, $anchor);

						$crop_data[$size]["x"] = abs($value["x"]) * $zoom_mod;
						$crop_data[$size]["y"] = abs($value["y"]) * $zoom_mod;
						$crop_data[$size]["width"] = $width;
						$crop_data[$size]["height"] = $height;
						$crop_data[$size]["zoom"] = $value["zoom"] * $zoom_mod;
					} else {
						$crop_data[$size]["x"] = 0;
						$crop_data[$size]["y"] = 0;
						$crop_data[$size]["zoom"] = 100;

						if ($width || $height) {
							$layers[$size]->resizeInPixel($width, $height, true);
						} elseif ($crop_data["max-width"] || $crop_data["max-height"]) {
							$size_ok = false;
							if (!$size_ok && isset($crop_data["max-width"])) {
								$layers[$size]->resizeInPixel($crop_data["max-width"], null, true);
								$img_height = $layers[$size]->getHeight();
								if (isset($crop_data["max-height"]) && $img_height < $crop_data["max-height"]) {
									$size_ok = true;
								}
							}
							if (!$size_ok && isset($crop_data["max-height"])) {
								$layers[$size]->resizeInPixel(null, $crop_data["max-height"], true);
								$img_width = $layers[$size]->getWidth();
								if (isset($crop_data["max-width"]) && $img_width < $crop_data["max-width"]) {
									$size_ok = true;
								}
							}
							if (!$size_ok && isset($crop_data["max-width"]) && isset($crop_data["max-height"])) {
								$largest = (($crop_data["max-width"] > $crop_data["max-height"])
									? $crop_data["max-width"] : $crop_data["max-height"]);
								$layers[$size]->resizeByLargestSideInPixel($largest, true);
							}
						}

						$crop_data[$size]["width"] = $layers[$size]->getWidth();
						$crop_data[$size]["height"] = $layers[$size]->getHeight();
					}

					$crop_path = $_SESSION["routerunner-config"]["MEDIA_ROOT"] .
						$crop_field . DIRECTORY_SEPARATOR . $path_route;
					$crop_dirPath = $_SESSION["routerunner-config"]['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .
						$_SESSION["routerunner-config"]["SITEROOT"] . $crop_path;
					$createFolders = true;
					$backgroundColor = null; // transparent, only for PNG (otherwise it will be white if set null)
					$imageQuality = 95; // useless for GIF, usefull for PNG and JPEG (0 to 100%)

					$crop_data[$crop_field] = $crop_path . $filename . "?t=" . time();

					$crop_data[$size] = json_encode($crop_data[$size]);

					$layers[$size]->save($crop_dirPath, $filename, $createFolders, $backgroundColor, $imageQuality);

					if (isset($crop_data["SQL"])) {
						foreach ($crop_data["SQL"] as $SQL => $var_names) {
							$params = array();
							foreach ($var_names as $index => $var_name) {
								if (isset($crop_data[$var_name])) {
									$params[$index] = $crop_data[$var_name];
								}
							}
							\db::query($SQL, $params);
						}
					}
				}
			}

			$image_field = str_replace("data_", "", $field);

			//$layer->resizeInPercent($value["zoom"], $value["zoom"], true);
			/*
			if (isset($value["canvasData"]) && is_array($value["canvasData"])) {
				$layer->resizeInPixel($value["canvasData"]["width"], $value["canvasData"]["height"], true, $value["canvasData"]["left"] * -1, $value["canvasData"]["top"] * -1, "LT");
			}
			*/
			$path = $_SESSION["routerunner-config"]["MEDIA_ROOT"] . $image_field . DIRECTORY_SEPARATOR . $path_route;
			$dirPath = $_SESSION["routerunner-config"]['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $_SESSION["routerunner-config"]["SITEROOT"] . $path;
			$createFolders = true;
			$backgroundColor = null; // transparent, only for PNG (otherwise it will be white if set null)
			$imageQuality = 95; // useless for GIF, usefull for PNG and JPEG (0 to 100%)

			$layer->cropInPixel($value["width"], $value["height"], $value["x"], $value["y"], "LT");

			if (isset($field_data["width"]) || isset($field_data["height"])) {
				$new_width = null;
				$new_height = null;
				$is_pixel = false;
				if (isset($field_data["width"])) {
					if (strpos($field_data["width"], "px") !== false) {
						$new_width = str_replace("px", "", $field_data["width"]);
						$is_pixel = true;
					} elseif (strpos($field_data["width"], "%") !== false) {
						$new_width = str_replace("%", "", $field_data["width"]);
						$is_pixel = false;
					}
				}
				if (isset($field_data["height"])) {
					if (strpos($field_data["height"], "px") !== false) {
						$new_height = str_replace("px", "", $field_data["height"]);
						$is_pixel = true;
					} elseif (strpos($field_data["height"], "%") !== false) {
						$new_height = str_replace("%", "", $field_data["height"]);
						$is_pixel = false;
					}
				}
				if ($new_width || $new_height) {
					if ($is_pixel) {
						$layer->resizeInPixel($new_width, $new_height, true);
					} else {
						$layer->resizeInPercent($new_width, $new_height, true);
					}
				}
			}

			$layer->save($dirPath, $filename, $createFolders, $backgroundColor, $imageQuality);

			$cropped_src = $path . $filename . "?t=" . time();

			$changes[$image_field] = $cropped_src;
		} else {
			// delete image
			$cropped_src = NULL;
		}
		$response["src"] = $cropped_src;
	}

	echo json_encode($response);
});