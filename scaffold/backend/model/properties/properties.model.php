<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 11:35
 */

namespace backend\model;

class properties extends \Routerunner\BaseModel {
	public $change_id;
	public $session;
	public $reference;
	public $changes;
	public $state;

	public $label;
	public $user;
	public $user_email;
	public $user_name;
	public $user_group;

	public $open;
	public $applied;
}