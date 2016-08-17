<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 11:35
 */

namespace backend\model;

class visibility extends \Routerunner\BaseModel {
	public $model_state_id;

	public $model;
	public $active;
	public $begin;
	public $end;
	public $params;
}