<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 11/11/2025
 * Time: 16:07
 */

namespace App\Modules\MobileApp\Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('mobile', ['namespace' => 'App\Modules\MobileApp\Controllers'], static function($routes) {
    $routes->get('/', 'Auth::login');
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::attemptLogin');
    $routes->get('logout', 'Auth::logout');
    $routes->get('home', 'Home::index');
});

$routes->group('mobile', ['namespace' => 'App\Modules\MobileApp\Controllers'], static function ($routes) {

    $routes->get('welcome', 'Welcome::index');
    $routes->get('lobby', 'Lobby::index');
    $routes->get('agenda/(:num)', 'Agenda::index/$1');
    $routes->get('session/(:num)', 'Session::detail/$1');
    $routes->get('webinars', 'Webinars::index');




});
