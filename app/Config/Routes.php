<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Load module routes dynamically
$modulesPath = APPPATH . 'Modules/';
$modules = scandir($modulesPath);

foreach ($modules as $module) {
    if ($module === '.' || $module === '..') continue;
    $routesPath = $modulesPath . $module . '/Config/Routes.php';
    if (is_file($routesPath)) {
        require $routesPath;
    }
}

$routes->get('redis-test', 'TestController::redis');
