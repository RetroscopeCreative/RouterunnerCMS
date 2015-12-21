<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.21.
 * Time: 14:48
 */

namespace Routerunner;

class CustomView extends \Slim\View
{
	public function display($template)
	{
		return $this->fetch($template);
	}

	public function render($template)
	{
		$static = \Routerunner\Routerunner::$static;
		$siteroot = $static->settings['SITEROOT'];
		$templatePathname = $siteroot . $this->getTemplatePathname($template);
		if (!is_file($templatePathname)) {
			throw new \RuntimeException("View cannot render `$template` because the template does not exist");
		}
		extract($this->data->all());
		ob_start();
		require $templatePathname;

		$return = ob_get_clean();

		return $return;
	}
}
