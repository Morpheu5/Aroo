<?php

function aroo_autoloader($className) {
	if(strstr($className, 'Aroo')) {
		require_once(str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php');
	}
}

spl_autoload_register('aroo_autoloader');

$application = new \Aroo\Application;
$application->bootstrap()->run();