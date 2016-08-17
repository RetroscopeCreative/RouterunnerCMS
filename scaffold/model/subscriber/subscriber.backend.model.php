<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.04.07.
 * Time: 16:33
 */
$debug = 1;
return array(
	//'wrapper_element' => 'wrap',
	'wrapper_class' => 'subscriber',
	//'wrapper_attr' => array('style' => 'border: 1px solid #900;'),
	'fields' => array(
		'label' => array(
			'selector' => '.brief h2 > a',
			'type' => 'contenteditable',
			'is_label' => true,

			'mandatory' => array(
				'value' => true,
				'msg' => 'Kötelező mező! Ne hagyja üresen!',
				//'holder' => 'selector',
			),
			'regexp' => array(
				'value' => '^.{5,}$',
				'options' => 'i',
				'msg' => 'Nem megfelelő formátum!',
				//'holder' => '',
			),

			'control' => array(
				'event' => array(
					'inline' => 'keyup',
					'panel' => 'change',
				),
				'value' => array(
					'inline' => 'html',
					'panel' => 'val',
				),
			),

			'input' => false, // str: function or array: function with params
			'output' => false, // str: function or array: function with params
		),
		'brief' => array(
			'selector' => '.prop-brief',
			'type' => 'ckeditor',
			'is_description' => true,

			'mandatory' => array(
				'value' => false,
				//'holder' => 'selector',
			),

			'control' => array(
				'init' => array(
					'inline' => 'init',
					'panel' => 'panel_init',
				),
				'event' => array(
					'inline' => 'inline_customevent',
					'panel' => 'panel_customevent',
				),
				'value' => array(
					'inline' => 'html',
					'panel' => 'ckhtml',
				),
			),


			'input' => false, // str: function or array: function with params
			'output' => false, // str: function or array: function with params
		),
		'content' => array(
			'selector' => '.prop-content',
			'type' => 'ckeditor',

			'mandatory' => array(
				'value' => false,
				//'holder' => 'selector',
			),

			'control' => array(
				'init' => array(
					'inline' => 'init',
					'panel' => 'panel_init',
				),
				'event' => array(
					'inline' => 'inline_customevent',
					'panel' => 'panel_customevent',
				),
				'value' => array(
					'inline' => 'html',
					'panel' => 'ckhtml',
				),
			),

			'input' => false, // str: function or array: function with params
			'output' => false, // str: function or array: function with params
		),
		'date' => array(
			'type' => 'date',

			'default' => time(),

			'help' => array(
				'panel' => 'Kérem válassza ki a dátumot!',
			),

			'input' => "strtotime", // str: function or array: function with params
			'output' => false, // str: function or array: function with params
		),
		'size_m' => array(
			'selector' => '.side-screenshot2',
			'type' => 'image',

			'control' => array(
				'selector' => 'img',
				'value' => 'src'
			),

			'input' => false, // str: function or array: function with params
			'output' => false, // str: function or array: function with params
		),
		'data_m' => array(
			'selector' => '.side-screenshot2',
			'type' => 'image_iviewer',

			'mediasize' => 'm',
			'width' => '532px',
			'height' => '407px',
			'control' => array(
				'event' => array(
					'inline' => 'inline_imagecrop',
					'panel' => 'null',
				),
				'value' => array(
					'inline' => 'imagecrop',
					'panel' => 'null',
				),
				'selector' => array(
					'inline' => 'img',
					'panel' => 'null',
				),
				'init' => array(
					'inline' => 'inline_init',
					'panel' => 'null',
				),
				'apply' => 'applycrop',
				'empty' => 'emptycrop',
			),
			'crop' => array(
				'width' => 532,
				'height' => 407,
				'SQL' => array(
					'UPDATE `references` SET `size_m` = ?, `data_m` = ? WHERE id = ?' => array(
						"size_m",
						"data_m",
						"id"
					),
				)
			),

			'input' => false, // str: function or array: function with params
			'output' => false, // str: function or array: function with params
		),
		'size_l' => array(
			'type' => 'image',

			'control' => array(
				'selector' => 'img',
				'value' => 'src'
			),

			'input' => false, // str: function or array: function with params
			'output' => false, // str: function or array: function with params
		),
		'data_l' => array(
			'type' => 'image_iviewer',

			'control' => array(
				'event' => array(
					'inline' => 'inline_imagecrop',
					'panel' => 'null',
				),
				'value' => array(
					'inline' => 'imagecrop',
					'panel' => 'null',
				),
				'selector' => array(
					'inline' => 'img',
					'panel' => 'null',
				),
				'init' => array(
					'inline' => 'inline_init',
					'panel' => 'null',
				),
				'apply' => 'applycrop',
				'empty' => 'emptycrop',
			),
			'crop' => array(
				'max-width' => 940,
				'max-height' => 600,
				'SQL' => array(
					'UPDATE `references` SET `size_l` = ?, `data_l` = ? WHERE id = ?' => array(
						"size_l",
						"data_l",
						"id"
					),
				)
			),

			'input' => false, // str: function or array: function with params
			'output' => false, // str: function or array: function with params
		),
	),
);