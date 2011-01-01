<?php

namespace Aroo;

class Router {
	private $routes = array();
	
	private $defaults = array(
		'path' => '/',
		'controller' => 'index',
		'action' => 'index'
	);
	
	function addRoute($route = array()) {
		$route = array_merge($this->defaults, $route);
		$matches = array();
		
		if(array_key_exists('params', $route)) {
			foreach($route['params'] as $param => $values) {
				if(array_key_exists('type', $values)) {
					switch($values['type']) {
					case 'integer':
						$route['path'] = str_replace(':' . $param, '([0-9]+)', $route['path']);
						break;
					case 'alpha':
						$route['path'] = str_replace(':' . $param, '([a-zA-Z-_]+)', $route['path']);
						break;
					case 'alnum':
					default:
						$route['path'] = str_replace(':' . $param, '([a-zA-Z0-9-_]+)', $route['path']);
					}
				}
				$matches[] = $param;
			}
		}
		$route['path'] = '/^' . str_replace('/', '\/', preg_replace('/^\//', '', $route['path'])) . '$/';
		$route['params'] = $matches;
		$this->routes[] = $route;
	}

	/* SINGLETON */
	
	private static $instance;
	
	public static function getInstance() {
		if(!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}
}