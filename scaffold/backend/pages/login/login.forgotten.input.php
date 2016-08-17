<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.02.18.
 * Time: 14:43
 */

$id = 0;
$method = 'post';
// method can be 'get', 'post', 'put', 'delete'


$form = array(
	'method' => 'post',
	'xmethod' => $method,
	'name' => 'forgotten',
	'error_format' => '<p class="err">%s</p>'.PHP_EOL,
	'from' => 'member',
	'condition' => array(
		array('member.id = :id', array(':id'=>'id'), 'AND'),
	),
);

$input = array(
	'email' => array(
		'label' => 'E-mail',
		'input' => 'email',
		'regexp' => '/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i',
		'errormsg' => 'Please type in your e-mail address!',
		'mandatory' => true,
		'placeholder' => 'E-mail',
	),
	'submitbtn' => array(
		'input' => 'submit',
		'value' => 'Send new password',
	),
);