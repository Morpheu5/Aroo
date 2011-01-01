<?php
/* Aroo
 * (C) by the authors.
 *
 * This file is part of Aroo.
 *
 * Aroo is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 *
 * Aroo is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Aroo. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Aroo;

class Router {
	private $routes = array();
	
	private $root_route = array();
	
	private $defaults = array(
		'path' => '/',
		'controller' => 'index',
		'action' => 'index',
		'params' => array()
	);
	
	function addRoute($route = array()) {
		$route = array_merge($this->defaults, $route);
		$matches = array();
		
		$route['regex'] = $route['path'];
		if(array_key_exists('params', $route)) {
			foreach($route['params'] as $param => $values) {
				if(array_key_exists('type', $values)) {
					switch($values['type']) {
					case 'integer':
						$route['regex'] = str_replace(':' . $param, '([0-9]+)', $route['regex']);
						break;
					case 'alpha':
						$route['regex'] = str_replace(':' . $param, '([a-zA-Z-_]+)', $route['regex']);
						break;
					case 'alnum':
					default:
						$route['regex'] = str_replace(':' . $param, '([a-zA-Z0-9-_]+)', $route['regex']);
					}
				}
				$matches[] = $param;
			}
		}
		$route['regex'] = '/^' . str_replace('/', '\/', preg_replace('/^\//', '', $route['regex'])) . '$/';
		$route['params'] = $matches;
		$this->routes[] = $route;
	}
	
	function setRoot($route = array()) {
		$route = array_merge($this->defaults);
		$this->root_route = $route;
	}
	
	function matchRequest($request = null) {
		if($request === null) {
			throw new Exception\Router('Request can\'t be null');
		}
		if($request == '') {
			// Follow the root route
			return $this->root_route;
		} else {
			// Do the matching
			foreach($this->routes as $route) {
				$params = array();
				if(preg_match($route['regex'], $request, $params)) {
					$param_names = $route['params'];
					$route['params'] = array_combine($param_names, array_slice($params, 1));
					return $route;
				}
			}
			throw new Exception\Router\RouteNotFound('The Request didn\'t match any route');
		}
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