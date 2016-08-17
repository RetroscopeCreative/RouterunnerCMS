<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 11:35
 */

namespace model;

class send extends \Routerunner\BaseModel {
	public $primary_key;
	public $id;
	public $campaign_id;
	public $label;
	public $category;
	public $active;
	public $sent;
	public $to_send;
	//public $opened;
	//public $clicked;
	public $test_address;
	public $limit_per_period;
	public $period;
	public $start;
	public $finish;
}