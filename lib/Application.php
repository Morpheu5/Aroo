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

require_once 'Spyc/spyc.php';

class Application {

	public function __construct() {
		spl_autoload_register(array($this, 'applicationAutoload'));
	}

	public function bootstrap() {
		// Load configuration
		$ymlCfg = \Spyc::YAMLLoad(realpath(APPLICATION_PATH . '/configs/application.yml'));
		$configuration = $ymlCfg[APPLICATION_ENV];
		if(array_key_exists('_inherits', $configuration)) {
			$configuration = array_merge($ymlCfg[$configuration['_inherits']], $configuration);
		}
		// If a temp directory has not been specified, follow the convention,
		// but allow the application to modify it later
		if(!array_key_exists('tempDir', $configuration)) {
			$tmpDir = realpath(APPLICATION_PATH . '/..');
			$configuration['tempDir'] = $tmpDir . '/tmp';
		}
		unset($configuration['_inherits']);
		Registry::set('aroo_configuration', $configuration);

		// Load routes
		$router = Router::getInstance();
		$ymlRoutes = \Spyc::YAMLLoad(realpath(APPLICATION_PATH . '/configs/routes.yml'));
		foreach($ymlRoutes['routes'] as $route) {
			$router->addRoute($route);
		}
		if(array_key_exists('root', $ymlRoutes)) {
			$router->setRoot($ymlRoutes['root']);
		}
		
		// Prepare the Request
		$basedir = dirname($_SERVER['SCRIPT_NAME']);
		$patterns = array(
			'/^' . str_replace('/', '\/', $basedir) . '/',
			'/\/+/',
			'/^\//',
			'/\/$/',
		);
		$replacements = array(
			'',
			'/',
			'',
			''
		);
		$request_uri = preg_replace($patterns, $replacements, $_SERVER['REQUEST_URI']);
		Registry::set('aroo_request_uri', $request_uri);
		Registry::set('aroo_request_method', $_SERVER['REQUEST_METHOD']);

		// Bootstrap the application
		if(@include_once APPLICATION_PATH . '/Bootstrap.php') {
			$appBootstrapClass = '\\' . $configuration['applicationNamespace'] . '\\' . 'Bootstrap';
			$appBootstrap = new $appBootstrapClass;
			$appBootstrap->bootstrap();
			
			$resources = array();
			foreach(get_class_methods($appBootstrapClass) as $method) {
				if(substr($method, 0, 4) == 'init') {
					$resource = $appBootstrap->$method();
				}
				if(isset($resource) && ($resource !== null)) {
					$resourceName = lcfirst(substr($method, 4));
					$resources[$resourceName] = $resource;
				}
			}
		}

		return $this;
	}

	public function run() {
		// At this point we should have a temp dir configured
		$config = Registry::get('aroo_configuration');
		if(!file_exists($config['tempDir'])) {
			throw new \Aroo\Exception\InvalidDirectory('Temporary directory "' . $config['tempDir'] . ' is not valid"');
		}
		// Take the request and pass it to the router
		$router = Router::getInstance();
		try {
			$result = $router->matchRequest(Registry::get('aroo_request_uri'));
		} catch(Exception\Router $e) {
			echo $e->getMessage();
		} catch(Exception\Router\RouteNotFound $e) {
			echo $e->getMessage();
		}
		// Dispatch the $result
		$dispatcher = Dispatcher::getInstance();
		$dispatcher->dispatch($result);

		return $this;
	}
	
	function applicationAutoload($className) {
		if(strstr($className, 'Aroo\\')) {
			require_once(str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php');
			return;
		}
		$config = Registry::get('aroo_configuration');
		if(strstr($className, $config['applicationNamespace'].'\\')) {
			$classArray = explode('\\', $className);
			switch($classArray[1]) {
			case 'Controller':
			case 'Model':
				// Try the conventional location
				if((@include_once APPLICATION_PATH . DIRECTORY_SEPARATOR . strtolower($classArray[1] . 's') . DIRECTORY_SEPARATOR . $classArray[2] . '.php')) {
					return;
				}
				// If it didn't work, try the configured locations
				if(array_key_exists('controllersPaths', $config)) {
					foreach($config['controllersPaths'] as $path) {
						if((@include_once realpath($path) . DIRECTORY_SEPARATOR . $classArray[2] . '.php')) {
							return;
						}
					}
				}
				if(array_key_exists('modelsPaths', $config)) {
					foreach($config['modelsPaths'] as $path) {
						if((@include_once realpath($path) . DIRECTORY_SEPARATOR . $classArray[2] . '.php')) {
							return;
						}
					}
				}
				break;
			default:
			}
		}
	}
}