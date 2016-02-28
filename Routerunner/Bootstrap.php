<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2013.12.09.
 * Time: 15:51
 */

namespace Routerunner;

class Bootstrap
{
	static $request;
	static $host;
	static $rootUri;
	static $resourceUri = '';
	static $ajaxResourceUri;
	static $baseUri;
	static $fullUri;
	static $relUri;
	static $method;
	static $isAjax;
	static $reference = null;

	static $urls = array();
	static $url_data = array();
	static $params = array();

	static $lang = null;
	static $metas = array(
		'title' => false,
		'keywords' => false,
		'description' => false,
		'meta' => array(
			'og:title' => false,
			'og:image' => false,
			'og:description' => false,
			'og:type' => false,
		),
	);
	static $states = array(
		'active' => 1,
		'begin' => null,
		'end' => null,
		'params' => array(),
	);
	static $tree = array(
		'parents' => array(),
		'children' => array(),
		'siblings' => array(),
	);

	static $history = array();

	static $resourceDelimiter = '/';
	static $resources;
	static $bootstrap = false;

	static $component = false;

	public static function initialize($settings, $breadcrumb = true)
	{
		if (!$breadcrumb) {
			self::$request = \Routerunner\Routerunner::$slim->request;

			self::$isAjax = self::$request->isAjax();
			self::$host = self::$request->getUrl();
			self::$rootUri = self::$request->getRootUri();

			$get = self::$request->get();
			$post = self::$request->post();
			if (isset($get, $post)) {
				self::$params = $get + $post;
			} elseif (isset($get)) {
				self::$params = $get;
			} elseif (isset($post)) {
				self::$params = $post;
			} else {
				self::$params = array();
			}

			if (self::$isAjax) {
				self::$ajaxResourceUri = self::$request->getResourceUri();
				$root = self::$host . self::$rootUri;
				if (isset(self::$params['url'])) {
					$root = substr($root, 0, strrpos($root, '/'));
					$plainUri = str_replace($root, '', self::$params['url']);
					$plainUri = substr($plainUri, 0, strpos($plainUri, '?'));
					self::$rootUri = substr(self::$rootUri, 0, strrpos(self::$rootUri, '/'));
					unset(self::$params['url']);
				} else {
					if (strpos($root, $settings['BASE']) !== false) {
						$plainUri = '/' . trim(str_replace($settings['BASE'], '', $root), '/');
					} else {
						$plainUri = $root;
					}
				}
			} else {
				$plainUri = self::$request->getResourceUri();
			}

			if (preg_match('/\.(jp(e)?g|png|gif|svg|css|js(on)?|woff(2)?|ttf|pdf|xml)$/i', $plainUri)) {
				self::$component = true;
			}

			$baseRoot = self::$host . self::$rootUri;
			if (!preg_match('/^(https?:\/\/)+/', $plainUri)) {
				$baseUri = trim($baseRoot, '/') . '/' . trim($plainUri, '/');
			} else {
				$baseUri = $plainUri;
			}
			$fullUri = $baseUri;
			if (self::$params) {
				$fullUri .= ((strpos($fullUri, '?') !== false) ? '&' : '?');
				$fullUri .= http_build_query(self::$params);
			}
			self::$baseUri = $baseUri;
			self::$fullUri = $fullUri;
			self::$relUri = str_replace($baseRoot, '', $fullUri);

			$env = \Routerunner\Routerunner::$slim->environment();

			$SQL = 'CALL `{PREFIX}rewrite_get`(:uri)';
			if ($rewrites = \Routerunner\Db::query($SQL, array(':uri' => trim($plainUri, '/ ')))) {
				foreach ($rewrites as $rewrite) {
					self::$urls[] = $rewrite['url'];
					self::$url_data[$rewrite['url']] = $rewrite;

					if (!self::$lang && !is_null($rewrite['lang'])) {
						self::$lang = $rewrite['lang'];
					}
					if (!self::$reference && !is_null($rewrite['reference'])) {
						self::$reference = $rewrite['reference'];
					}
					if (!self::$resourceUri && !is_null($rewrite['resource_uri'])) {
						self::$resourceUri = $rewrite['resource_uri'];
					}
					if (preg_match('/^(https?:\/\/)+/', self::$resourceUri)) {
						self::$resourceUri = '/';
					}

					$env->offsetSet('PATH_INFO', self::$resourceUri);

					if (isset($rewrite['params']) && !is_null($rewrite['params'])) {
						$params = json_decode($rewrite['params'], true);
						if (is_array($params))
							$env->offsetSet('QUERY_STRING', http_build_query($params, '', '&'));
					}
				}
			} else {
				self::$resourceUri = trim($plainUri, '/ ');
			}
			if (!self::$resourceUri) {
				self::$resourceUri = '/';
			}
			if (!self::$lang && \runner::config('language')) {
				self::$lang = \runner::config('language');
			} elseif (self::$reference) {
				self::$lang = self::lang(self::$reference);
			} elseif ($lang_result =
				\db::query('SELECT id FROM {PREFIX}lang WHERE code = ?', array(trim($plainUri, '/')))) {
				self::$lang = $lang_result[0]['id'];
			} else {
				self::$lang = 1;
			}
			\runner::config('language', self::$lang);

			if (self::$reference) {
				$SQL = 'CALL `{PREFIX}metas_get`(:reference)';
				if ($metas = \Routerunner\Db::query($SQL, array(':reference' => self::$reference))) {
					$metas = $metas[0];
					$meta_other = $metas['meta'];
					unset($metas['meta']);

					self::$metas = array_merge(self::$metas, $metas);
					if (self::$metas['meta'] && ($meta = json_decode($meta_other, true))) {
						self::$metas['meta'] = array_merge(self::$metas['meta'], $meta);
					}
				}

				$SQL = 'CALL `{PREFIX}states_get`(:reference)';
				if ($states = \Routerunner\Db::query($SQL, array(':reference' => self::$reference))) {
					self::$states = $states[0];
					if (self::$states['params'] && ($state_params = json_decode(self::$states['params'], true))) {
						self::$states['params'] = $state_params;
					}
				}
			}
			self::$tree = self::getTree(self::$reference ? self::$reference : 0);

			self::$method = self::$request->getMethod();

			if (isset($settings['resourceDelimiter']))
				self::$resourceDelimiter = $settings['resourceDelimiter'];

			self::$resources = explode(self::$resourceDelimiter, trim(self::$resourceUri, self::$resourceDelimiter));

			self::set_method();
		}


		if ($breadcrumb) {
			self::load_breadcrumb();
		}
	}

	public static function getResource()
	{
		if (!self::$bootstrap) {
			self::load_breadcrumb();
		}
		if (isset(self::$resources) && is_array(self::$resources) && count(self::$resources)
			/*&& in_array(self::$resources[0], self::bootstrap("resource_types"))*/) {
			$return = ((self::$isAjax && self::$ajaxResourceUri && self::$ajaxResourceUri != "/")
				? self::$ajaxResourceUri : self::$resourceUri);
		} else {
			$return = false;
		}
		return $return;
	}

	public static function getMethod()
	{
		return strtolower(self::$method);
	}

	private static function set_method()
	{
		\Routerunner\Routerunner::$static->request = strtolower(self::$method);
	}

	public static function getTree($reference)
	{
		$current_index = $reference;
		$tree = array(
			'parents' => self::parent($reference),
			'children' => self::children($reference),
			'siblings' => self::siblings($reference, false, $current_index),
			'current' => null,
			'language' => self::lang($reference),
		);
		if (!is_null($current_index) && isset($tree['siblings'][$current_index])) {
			$tree['current'] = $tree['siblings'][$current_index];
		}
		return $tree;
	}

	public static function lang($reference)
	{
		$SQL = 'CALL `{PREFIX}tree_lang`(:reference)';
		if ($lang = \Routerunner\Db::query($SQL, array(':reference' => $reference))) {
			return ((isset($lang[0]["lang"]) && is_numeric($lang[0]["lang"])) ? $lang[0]["lang"] : false);
		}
		return false;
	}
	public static function parent($reference, & $treeroot=false, & $route=array())
	{
		$SQL = 'CALL `{PREFIX}tree_parent`(:reference, :session)';
		if ($parents = \Routerunner\Db::query($SQL, array(
			':reference' => $reference,
			':session' => \runner::stack('session_id'),
		))) {
			if (count($parents)) {
				$_temp_parents = $parents;
				while ($_temp_parent = array_shift($_temp_parents)) {
					if ($_temp_parent["model_class"] != "lang") {
						$route[] = $_temp_parent["model_class"];
					}
					if ($_temp_parent["model_class"] == "tree") {
						$treeroot = $_temp_parent;
						//$parents = $_temp_parents;
					}
				}
			}
			return $parents;
		}
		return array();
	}
	public static function children($reference, $lang=false)
	{
		$SQL = 'CALL `{PREFIX}tree_children`(:reference, :lang, NULL, NULL, :session)';
		if ($children = \Routerunner\Db::query($SQL, array(
			':reference' => $reference,
			':lang' => ($lang ? $lang : NULL),
			':session' => (\runner::stack('session_id') ? \runner::stack('session_id') : NULL)))) {
			return $children;
		}
		return array();
	}
	public static function siblings($reference, $lang=false, & $find=null)
	{
		$SQL = 'CALL `{PREFIX}tree_siblings`(:reference, :lang, NULL, NULL, :session)';
		if ($siblings = \Routerunner\Db::query($SQL, array(
			':reference' => $reference,
			':lang' => ($lang ? $lang : NULL),
			':session' => \runner::stack('session_id')))) {
			if (!is_null($find)) {
				$found = null;
				foreach ($siblings as $index => $sibling) {
					if ($sibling['reference'] == $find) {
						$found = $index;
						break;
					}
				}
				$find = $found;
			}
			return $siblings;
		}
		return array();
	}

	private static function load_breadcrumb()
	{
		$root = \Routerunner\Routerunner::$static->config("root");
		$second_root = \Routerunner\Routerunner::$static->config("second_root");
		if (!self::$bootstrap && (isset($root) || isset($second_root))) {
			$class = ((strpos($root, DIRECTORY_SEPARATOR) !== false)
				? substr($root, strrpos($root, DIRECTORY_SEPARATOR) + 1) : $root);
			$second_class = ($second_root && (strpos($second_root, DIRECTORY_SEPARATOR) !== false)
				? substr($second_root, strrpos($second_root, DIRECTORY_SEPARATOR) + 1) : $second_root);
			$suffix = ((strpos($root, DIRECTORY_SEPARATOR) !== false)
				? substr($root, 0, strrpos($root, DIRECTORY_SEPARATOR) + 1) : '');
			$second_suffix = \Routerunner\Routerunner::$static->config("second_suffix");
			if (!$second_suffix) {
				$second_suffix = ((strpos($second_root, DIRECTORY_SEPARATOR) !== false)
					? substr($second_root, 0, strrpos($second_root, DIRECTORY_SEPARATOR) + 1) : '');
			}
			if (\Routerunner\Common::inc('bootstrap', $class.'.bootstrap', false, false, false, $suffix)) {
				$ns_class = '\\' . $class . '\\bootstrap';
				self::$bootstrap = new $ns_class();
			} elseif (\Routerunner\Common::inc('bootstrap', $second_class.'.bootstrap', false, false, false, $second_suffix)) {
				$ns_class = '\\' . $second_class . '\\bootstrap';
				self::$bootstrap = new $ns_class();
			}
			if (!self::$bootstrap) {
				$class = 'default';
				if (\Routerunner\Common::inc('bootstrap', $class.'.bootstrap', false, false, false, $suffix)) {
					$ns_class = '\\' . $class . '\\bootstrap';
					self::$bootstrap = new $ns_class();
				}
			}
		}
	}

	public static function breadcrumb($node=null)
	{
		if ((!is_null($node)) && isset(self::$bootstrap->breadcrumb[$node])) {
			return self::$bootstrap->breadcrumb[$node];
		} elseif (isset(self::$bootstrap) && isset(self::$bootstrap->breadcrumb)) {
			return self::$bootstrap->breadcrumb;
		} else {
			return array();
		}
	}

	public static function bootstrap($node=null)
	{
		if ((!is_null($node)) && isset(self::$bootstrap->$node)) {
			return self::$bootstrap->$node;
		} elseif ((!is_null($node)) && isset(self::$bootstrap->breadcrumb[$node])) {
			return self::$bootstrap->breadcrumb[$node];
		} elseif (isset(self::$bootstrap)) {
			return self::$bootstrap;
		} else {
			return array();
		}
	}
}