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

class Dispatcher {
	
	function dispatch($route = array()) {
		if(empty($route)) {
			throw new Exception\Dispatcher\UndefinedRoute('Undefined route');
		}
		$config = Registry::get('aroo_configuration');
		$controllerClass = '\\'.$config['applicationNamespace'].'\\Controller\\' . ucfirst(strtolower($route['controller']));
		$actionName = strtolower($route['action']);
		$params = $route['params'];
		
		$controllerObject = new $controllerClass;
		$controllerObject->setParams($params);
		$controllerObject->$actionName();
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