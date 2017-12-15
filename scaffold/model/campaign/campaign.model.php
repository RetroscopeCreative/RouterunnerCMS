<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 11:35
 */

namespace model;

class campaign extends \Routerunner\BaseModel {
	public $id;
	public $label;
	public $category;
	public $active;
	public $subject;
	public $mail_html;
	public $mail_text;
	public $sent;
	public $to_send;
	public $opened;
	public $clicked;
}