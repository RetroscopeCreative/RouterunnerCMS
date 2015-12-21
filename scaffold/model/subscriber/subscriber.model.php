<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 11:35
 */

namespace model;

class subscriber extends \Routerunner\BaseModel {
	public $id;
	public $label;
	public $date;
	public $link;
	public $email;
	public $category;
	public $unsubscribe;
}