<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 11:35
 */

namespace backend\model;

class drafts extends \Routerunner\BaseModel {
	public $id;
	public $reference;
	public $date;
	public $user;
	public $model;
	public $approved;
	public $approver;
}