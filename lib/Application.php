<?php

namespace Aroo;

require_once 'spyc/spyc.php';

class Application {
	public function bootstrap() {
		// Load configuration
		$ymlCfg = \Spyc::YAMLLoad(realpath(APPLICATION_PATH . '/configs/application.yml'));
		$configuration = $ymlCfg[APPLICATION_ENV];
		if(array_key_exists('_inherits', $configuration)) {
			$configuration = array_merge_recursive($configuration, $ymlCfg[$configuration['_inherits']]);
		}
		
		// Load routes
		$router = Router::getInstance();
		$ymlRoutes = \Spyc::YAMLLoad(realpath(APPLICATION_PATH . '/configs/routes.yml'));
		foreach($ymlRoutes as $route) {
			$router->addRoute($route);
		}
		
		return $this;
	}
	
	public function run() {
		// Take the request and pass it to the router
	}
}