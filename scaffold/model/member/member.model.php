<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 11:35
 */

namespace model;

class member extends \Routerunner\BaseModel {
	public $id;
	public $email;
	public $reg_date;
	public $confirm_date;
	public $last_login;
	public $last_ip;
	public $licence;
	public $usergroup_id;
	public $usergroup_label;
}