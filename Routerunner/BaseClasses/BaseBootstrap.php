<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.12.09.
 * Time: 16:00
 */
namespace Routerunner;

class BaseBootstrap
{
	public function getParent($child)
	{
		// some correct & designed pattern for hierarchy
		return $child-1;
	}
	public function getChild($parent)
	{
		// some correct & designed pattern for hierarchy
		return $parent+1;
	}
}