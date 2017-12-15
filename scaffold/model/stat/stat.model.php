<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 11:35
 */

namespace model;

class stat extends \Routerunner\BaseModel {
	public $stat_id;
	public $activity_date;
	public $activity;
	public $clicked;
	public $name;
	public $email;
	public $category;
	public $unsubscribe_date;
	public $send_date;
	public $campaign_label;
	public $subject;
}