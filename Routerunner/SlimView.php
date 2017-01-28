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
		//$siteroot = $static->settings['SITEROOT'];
		//$templatePathname = $siteroot . $this->getTemplatePathname($template);
		$templatePathname = $this->getTemplatePathname($template);
		if (!is_file($templatePathname)) {
		    if (is_file(\runner::config('DOCUMENT_ROOT') . DIRECTORY_SEPARATOR . $templatePathname)) {
		        $templatePathname = \runner::config('DOCUMENT_ROOT') . DIRECTORY_SEPARATOR . $templatePathname;
            } else {
                throw new \RuntimeException("View cannot render `$template` because the template does not exist");
            }
		}
		extract($this->data->all());
		ob_start();
		require $templatePathname;

		$return = ob_get_clean();

		return $return;
	}
}
