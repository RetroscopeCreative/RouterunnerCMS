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
	'name' => 'signin',
	'error_format' => '<p class="err">%s</p>'.PHP_EOL,
	'from' => 'member',
	'condition' => array(
		array('member.id = :id', array(':id'=>'id'), 'AND'),
	),
);

$input = array(
	'email' => array(
		'label' => 'E-mail',
		'type' => 'email',
		'mandatory' => array(
			'value' => true,
			'msg' => 'Please type in your e-mail address!',
		),
		'regexp' => array(
			'value' => '[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}',
			'options' => 'i',
			'msg' => 'Invalid e-mail format!',
		),
		'placeholder' => 'E-mail',
	),
	'password' => array(
		'label' => 'Password',
		'type' => 'password',
		'mandatory' => array(
			'value' => true,
			'msg' => 'Please type in your password!',
		),
		'regexp' => array(
			'value' => '.{6,}',
			'options' => '',
			'msg' => 'Invalid password format!',
		),
		'placeholder' => 'Password',
		'function'=>'logincrypt',
	),
	'rememberme' => array(
		'label' => 'Remember me',
		'type' => 'checkbox',
		'placeholder' => '',
		'value' => 1,
	),
	'submitbtn' => array(
		'type' => 'submit',
		'value' => 'Login',
	),

);