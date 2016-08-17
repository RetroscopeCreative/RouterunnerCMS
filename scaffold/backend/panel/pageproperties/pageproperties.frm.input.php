<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.05.07.
 * Time: 19:14
 */
$bootstrap = \bootstrap::get();

$form = array(
	'method' => 'post',
	'xmethod' => 'put',
	'name' => 'routerunner-page-properties',
	'error_format' => '<p class="err">%s</p>'.PHP_EOL,
	'from' => '{PREFIX}rewrite',
	'condition' => array(
		array('rewrite_id = :az', array(':az'=>'az'), 'AND'),
	),
);

$meta = array_merge(array(
	"social-title" => "",
	"social-image" => "",
	"social-description" => "",
	"social-type" => "",
), $bootstrap->pageproperties["meta"]);

$og_image = '';
if (isset($meta["og:image"]) && $meta["og:image"] && file_exists($meta["og:image"])) {
	$og_image = $meta["og:image"];
}

$input = array(
	 'reference' => array(
		 'type' => 'hidden',
		 'field' => 'reference',
		 'value' => $bootstrap->reference
	 ),
	 'resource_uri' => array(
		 'type' => 'hidden',
		 'field' => 'resource_uri',
		 'value' => $bootstrap->resource_url
	 ),
	 'params' => array(
		 'type' => 'hidden',
		 'field' => 'params',
		 'value' => http_build_query($bootstrap->params)
	 ),
	 'lang' => array(
		 'type' => 'hidden',
		 'field' => 'lang',
		 'value' => $bootstrap->lang
	 ),
	 'title' => array(
		 'selector' => '#title',
		 'type' => 'text',
		 'field' => 'title',
		 'label' => 'Page title',

		 'input-id' => 'title',
		 'class' => '',
		 'class-inner' => 'title pageprop-sizable',
		 'icon' => '<span class="fa fa-info"></span>',
		 /*
		 'mandatory' => array(
			 'value' => true,
			 'msg' => 'Mandatory field!',
		 ),
		 'regexp' => array(
			 'value' => '^.{6,}$',
			 'options' => 'i',
			 'msg' => 'Not long enough!',
		 ),
		 */
		 'help' => '<span class="help-block">Please give the title of the page!</span>',

		 'change' => array(
			 'event' => 'change',
			 'value' => 'val',
			 'call' => array(
				 'object' => 'routerunner.page.pageproperties',
				 'function' => 'change'
			 ),
		 ),

		 'default' => $bootstrap->pageproperties_default["title"],
		 'value' => $bootstrap->pageproperties["title"],
	 ),
	 'url' => array(
		 'selector' => '#url',
		 'type' => 'text',
		 'field' => 'url',
		 'label' => 'Page URL',

		 'input-id' => 'url',
		 'class' => '',
		 'class-inner' => 'url pageprop-sizable',
		 'icon' => '<span class="fa fa-external-link"></span>',
		 /*
		 'mandatory' => array(
			 'value' => true,
			 'msg' => 'Mandatory field!',
		 ),
		 'regexp' => array(
			 'value' => '^[\w\d-.]{6,}$',
			 'options' => 'i',
			 'msg' => 'Invalid url format!',
		 ),
		 */
		 'help' => '<span class="help-block">Please give the url of the page!</span>',

		 'change' => array(
			 'event' => 'change',
			 'value' => 'val',
			 'call' => array(
				 'object' => 'routerunner.page.pageproperties',
				 'function' => 'change'
			 ),
		 ),

		 'input' => false, // check if it's unique
		 'output' => false, // str: function or array: function with params

		 'default' => $bootstrap->resource_url,
		 'value' => $bootstrap->url
	 ),
	 'keywords' => array(
		 'selector' => '#keywords',
		 'type' => 'textarea',
		 'field' => 'keywords',
		 'label' => 'Keywords:',

		 'input-id' => 'keywords',
		 'class-inner' => '',
		 /*
		 'regexp' => array(
			 'value' => '^.{1,}$',
			 'options' => 'gim',
			 'msg' => 'Nem megfelelő formátum!',
		 ),
		 'error' => array(
			'selector' => '.help-block',
            'template' => '{text} <span class="label label-sm label-danger">{key} <span class="fa fa-exclamation-circle"></span></span>',
            'method' => 'replaceWith',
		 ),
		*/
		 'help' => '<span class="help-block">Please give the url of the page!</span>',

		 'change' => array(
			 'event' => 'change',
			 'value' => 'val',
			 'call' => array(
				 'object' => 'routerunner.page.pageproperties',
				 'function' => 'change'
			 ),
		 ),

		 'input' => false, // str: function or array: function with params
		 'output' => false, // str: function or array: function with params

		 'default' => $bootstrap->pageproperties_default["keywords"],
		 'value' => $bootstrap->pageproperties["keywords"]
	 ),
	 'urls' => array(
		 'selector' => '#urls',
		 'type' => 'textarea',
		 'field' => 'urls',
		 'label' => 'Other url-s:',

		 'input-id' => 'urls',
		 'class-inner' => '',
		 /*
		 'regexp' => array(
			 'value' => '^[\w\d-.]{1,}$',
			 'options' => 'gim',
			 'msg' => 'Nem megfelelő formátum!',
		 ),
		 'error' => array(
			 'selector' => '.help-block',
			 'template' => '<span class="help-block">{text} <span class="label label-sm label-danger">{key} <span class="fa fa-exclamation-circle"></span></span></span>',
			 'method' => 'replaceWith',
			 'addClass' => array('' => 'has-error'),
		 ),
		*/
		 'help' => '<span class="help-block">Please give the url of the page!</span>',

		 'change' => array(
			 'event' => 'change',
			 'value' => 'val',
			 'call' => array(
				 'object' => 'routerunner.page.pageproperties',
				 'function' => 'change'
			 ),
		 ),

		 'input' => false, // str: function or array: function with params
		 'output' => false, // str: function or array: function with params

		 'value' => implode(PHP_EOL, $bootstrap->urls)
	 ),
	 'description' => array(
		 'selector' => '#description',
		 'type' => 'textarea',
		 'field' => 'description',
		 'label' => 'Description:',

		 'input-id' => 'description',
		 'class-inner' => '',

		 'help' => '<span class="help-block">Please give the url of the page!</span>',

		 'change' => array(
			 'event' => 'change',
			 'value' => 'val',
			 'call' => array(
				 'object' => 'routerunner.page.pageproperties',
				 'function' => 'change'
			 ),
		 ),

		 'input' => false, // str: function or array: function with params
		 'output' => false, // str: function or array: function with params

		 'default' => $bootstrap->pageproperties_default["description"],
		 'value' => $bootstrap->pageproperties["description"]
	 ),
	 'og-title' => array(
		 'selector' => '#og-title',
		 'type' => 'text',
		 'field' => 'og:title',
		 'label' => 'Title:',

		 'input-id' => 'og-title',
		 'class-inner' => '',

		 'help' => '<span class="help-block">Here comes the title for facebook</span>',

		 'change' => array(
			 'event' => 'change',
			 'value' => 'val',
			 'call' => array(
				 'object' => 'routerunner.page.pageproperties',
				 'function' => 'change'
			 ),
		 ),

		 'input' => false, // str: function or array: function with params
		 'output' => false, // str: function or array: function with params

		 'value' => $meta["og:title"]
	 ),
	/*
	 'og-image' => array(
		 'selector' => '#og-image',
		 'type' => 'image',
		 'field' => 'og:image',
		 'label' => 'Image:',

		 'input-id' => 'og-image',
		 'class-inner' => 'og-image',

		 'help' => '<span class="help-block">Here comes the image for facebook</span>',

		 'change' => array(
			 'event' => 'change',
			 'value' => 'val',
			 'call' => array(
				 'object' => 'routerunner.page.pageproperties',
				 'function' => 'change'
			 ),
		 ),

		 'input' => false, // str: function or array: function with params
		 'output' => false, // str: function or array: function with params

		 'value' => $og_image
	 ),
	*/
	 'og-description' => array(
		 'selector' => '#og-description',
		 'type' => 'textarea',
		 'field' => 'og:description',
		 'label' => 'Description:',

		 'input-id' => 'og-description',
		 'class-inner' => '',

		 'help' => '<span class="help-block">Here comes the description for facebook</span>',

		 'change' => array(
			 'event' => 'change',
			 'value' => 'val',
			 'call' => array(
				 'object' => 'routerunner.page.pageproperties',
				 'function' => 'change'
			 ),
		 ),

		 'input' => false, // str: function or array: function with params
		 'output' => false, // str: function or array: function with params

		 'value' => $meta["og:description"]
	 ),
	 'og-type' => array(
		 'selector' => '#og-type',
		 'type' => 'select',
		 'field' => 'og:type',
		 'label' => 'Type:',

		 'input-id' => 'og-type',
		 'class-inner' => '',

		 'help' => '<span class="help-block">Here comes the type for facebook</span>',

		 'change' => array(
			 'event' => 'change',
			 'value' => 'val',
			 'call' => array(
				 'object' => 'routerunner.page.pageproperties',
				 'function' => 'change'
			 ),
		 ),

		 'options' => array(
			 "article" => "Article",
			 "book" => "Book",
			 "profile" => "Profile",
			 "video" => "Video",
			 "website" => "Website",
			 "music" => "Music",
		 ),

		 'input' => false, // str: function or array: function with params
		 'output' => false, // str: function or array: function with params

		 'value' => $meta["og:type"]
	 ),

 );
$debug = 1;