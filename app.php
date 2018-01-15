<?php

//Cron to run every minute 
// * * * * * /infinito/script.sh

require_once('helper.php');
//require_once('vendor/autoload.php');

/**
 * Autoloader
 * @param string $class
 */
function app_autoloader($class) {
	$basePath = realpath(dirname(__FILE__));
    $filename = $basePath . '/src/' . str_replace('\\', '/', $class) . '.php';
    include($filename);
}
spl_autoload_register('app_autoloader');

App\App::init();
App\App::handler();



