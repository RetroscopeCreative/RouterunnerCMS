<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.15.
 * Time: 11:35
 */

namespace backend\tree;

class model extends \Routerunner\BaseModel {
	public $id;
	public $lang;
	public $parent;

	public $reference;
	public $model_class;
	public $table_id;

	public $accept;
}