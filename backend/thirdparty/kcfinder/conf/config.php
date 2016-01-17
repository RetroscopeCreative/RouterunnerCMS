<?php

/** This file is part of KCFinder project
  *
  *      @desc Base configuration file
  *   @package KCFinder
  *   @version 3.12
  *    @author Pavel Tzonkov <sunhater@sunhater.com>
  * @copyright 2010-2014 KCFinder Project
  *   @license http://opensource.org/licenses/GPL-3.0 GPLv3
  *   @license http://opensource.org/licenses/LGPL-3.0 LGPLv3
  *      @link http://kcfinder.sunhater.com
  */

/* IMPORTANT!!! Do not comment or remove uncommented settings in this file
   even if you are using session configuration.
   See http://kcfinder.sunhater.com/install for setting descriptions */
$debug=1;
return array(


// GENERAL SETTINGS

    'disabled' => false,
    'uploadURL' => (isset($_SESSION["routerunner-config"]["BASE"])
        ? $_SESSION["routerunner-config"]["BASE"] : "upload"),
    'uploadDir' => (isset($_SESSION["routerunner-config"]["SITEROOT"])
        ? $_SESSION["routerunner-config"]["SITEROOT"] : "upload"),
    'theme' => "default",
/*
    'types' => array(

    // (F)CKEditor types
        'files'   =>  "",
        'flash'   =>  "swf",
        'images'  =>  "*img",

    // TinyMCE types
        'file'    =>  "",
        'media'   =>  "swf flv avi mpg mpeg qt mov wmv asf rm",
        'image'   =>  "*img",
    ),
*/
	'types' => array(
		(isset($_SESSION["routerunner-config"]["MEDIA_ROOT"])
			? trim($_SESSION["routerunner-config"]["MEDIA_ROOT"], "/") : "content") => array(
			'type' => (isset($_SESSION["routerunner-config"]["MEDIA_TYPE"])
				? $_SESSION["routerunner-config"]["MEDIA_TYPE"] : "*img"),
			'thumbWidth' => 200,
			'thumbHeight' => 200
		),
	),
// IMAGE SETTINGS

    'imageDriversPriority' => "imagick gmagick gd",
    'jpegQuality' => 90,
    'thumbsDir' => ".thumbs",

    'maxImageWidth' => 0,
    'maxImageHeight' => 0,

    'thumbWidth' => 100,
    'thumbHeight' => 100,

    'watermark' => "",


// DISABLE / ENABLE SETTINGS

    'denyZipDownload' => false,
    'denyUpdateCheck' => true,
    'denyExtensionRename' => false,


// PERMISSION SETTINGS

    'dirPerms' => 0755,
    'filePerms' => 0644,

    'access' => array_merge(array(

        'files' => array(
            'upload' => false,
            'delete' => false,
            'copy'   => false,
            'move'   => false,
            'rename' => false
        ),

        'dirs' => array(
            'create' => false,
            'delete' => false,
            'rename' => false
        )
    ), (isset($_SESSION["routerunner-config"]["media_access"])
        ? $_SESSION["routerunner-config"]["media_access"] : array())),

    'deniedExts' => "exe com msi bat cgi pl php phps phtml php3 php4 php5 php6 py pyc pyo pcgi pcgi3 pcgi4 pcgi5 pchi6",


// MISC SETTINGS

    'filenameChangeChars' => array(/*
        ' ' => "_",
        ':' => "."
    */),

    'dirnameChangeChars' => array(/*
        ' ' => "_",
        ':' => "."
    */),

    'mime_magic' => "",

    'cookieDomain' => "",
    'cookiePath' => "",
    'cookiePrefix' => 'KCFINDER_',


// THE FOLLOWING SETTINGS CANNOT BE OVERRIDED WITH SESSION SETTINGS

    '_sessionVar' => "KCFINDER",
    '_check4htaccess' => false,
    '_normalizeFilenames' => false,
    '_dropUploadMaxFilesize' => 10485760,
    //'_tinyMCEPath' => "/tiny_mce",
    //'_cssMinCmd' => "java -jar /path/to/yuicompressor.jar --type css {file}",
    //'_jsMinCmd' => "java -jar /path/to/yuicompressor.jar --type js {file}",
);
