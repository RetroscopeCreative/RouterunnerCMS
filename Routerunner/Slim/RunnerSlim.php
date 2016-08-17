<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.11.21.
 * Time: 15:06
 */

namespace Slim;

class RunnerSlim extends Slim
{
	/**
	 * Slim PSR-0 autoloader
	 */
	public static function autoload($className)
	{
		$thisClass = str_replace(__NAMESPACE__.'\\', '', __CLASS__);

		$baseDir = __DIR__;

		if (substr($baseDir, -strlen($thisClass)) === $thisClass) {
			$baseDir = substr($baseDir, 0, -strlen($thisClass));
		} elseif ($thisClass === "RunnerSlim" && substr($baseDir, -4) === "Slim") {
			$baseDir = substr($baseDir, 0, -4);
		}

		$className = ltrim($className, '\\');
		$fileName  = $baseDir;
		$namespace = '';
		if ($lastNsPos = strripos($className, '\\')) {
			$namespace = substr($className, 0, $lastNsPos);
			if ($namespace === "Routerunner" && substr(rtrim($baseDir, '\\/'), -11) === "Routerunner") {
				$fileName = substr(rtrim($baseDir, '\\/'), 0, -11);
			}
			$className = substr($className, $lastNsPos + 1);
			$fileName  .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}
		$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

		if (file_exists($fileName)) {
			require $fileName;
		}
	}

	/**
	 * Register Slim's PSR-0 autoloader
	 */
	public static function registerAutoloader()
	{
		spl_autoload_register(__NAMESPACE__ . "\\RunnerSlim::autoload");
	}

	public function render($template, $data = array(), $status = null)
	{
		if (!is_null($status)) {
			$this->response->status($status);
		}
		$this->view->setTemplatesDirectory($this->config('templates.path'));
		$this->view->appendData($data);
		return $this->view->display($template);
	}

	public function flash($key, $value=null, $now=false)
	{
		$return = null;
		if (isset($this->environment['slim.flash'])) {
			if (!is_null($value)) {
				if ($now) {
					$this->environment['slim.flash']->now($key, $value);
				} else {
					$this->environment['slim.flash']->set($key, $value);
				}
				$return = $value;
			} else {
				$flash = $this->environment['slim.flash'];
				$return = $flash->getMessages();
				$return  = ((isset($return[$key])) ? $return[$key] : null);
			}
		}
		return $return;
	}

	public function now($key, $value=null)
	{
		return $this->flash($key, $value, true);
	}

	public function halt($status, $message = '')
	{
		if ($status !== 200) {
			$this->cleanBuffer();
		}
		$this->response->status($status);
		$this->response->body($message);
		if ($status !== 200) {
			$this->stop();
		}
	}

	public function stop()
	{
		throw new \Slim\Exception\Stop();
	}

	/**
	 * Call
	 *
	 * This method finds and iterates all route objects that match the current request URI.
	 */
	public function call()
	{
		try {
			if (isset($this->environment['slim.flash'])) {
				$this->view()->setData('flash', $this->environment['slim.flash']);
			}
			$this->applyHook('slim.before');
			ob_start();
			$this->applyHook('slim.before.router');
			$dispatched = false;
			$matchedRoutes = $this->router->getMatchedRoutes($this->request->getMethod(), $this->request->getResourceUri());
			foreach ($matchedRoutes as $route) {
				try {
					$this->applyHook('slim.before.dispatch');
					$dispatched = $route->dispatch();
					$this->applyHook('slim.after.dispatch');
					if ($dispatched) {
						break;
					}
				} catch (\Slim\Exception\Pass $e) {
					continue;
				}
			}
			if (!$dispatched) {
				$this->notFound();
			}
			$this->applyHook('slim.after.router');
			//$this->stop();
		} catch (\Slim\Exception\Stop $e) {
			$this->response()->write(ob_get_clean());
			$this->applyHook('slim.after');
		} catch (\Exception $e) {
			if ($this->config('debug')) {
				$log = $this->getLog();
				$log->emergency($e);
			} else {
				try {
					$this->error($e);
				} catch (\Slim\Exception\Stop $e) {
					print_r($e);
					// Do nothing
				}
			}
		}
	}

}
