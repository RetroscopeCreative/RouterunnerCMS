<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 11:03
 */

namespace backend\tree\model;

class runner extends \Routerunner\BaseRunner
{
	public $permissions = array(
		array('owner' => 1000, 'group' => 100, 'permission' => 15),
		array('other' => 1, 'permission' => 1),
	);
}
