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
    $routes->get('register', 'RegisterController::index');
    $routes->post('register/process', 'RegisterController::process');
});


$routes->group('mobile', ['namespace' => 'App\Modules\MobileApp\Controllers'], ['filter' => 'mobileauth'], function ($routes) {
    $routes->get('welcome', 'Welcome::index');
    $routes->get('home', 'Home::index');
    $routes->get('lobby', 'Lobby::index');
    $routes->get('agenda/(:num)', 'Agenda::index/$1');
    $routes->get('session/(:num)', 'Session::detail/$1');
    $routes->get('webinars', 'Webinars::index');
    $routes->get('envision', 'Home::envision');

    $routes->get('pastConference/(:num)', 'PastConference::index/$1');
    $routes->get('pastConference/session/(:num)', 'PastConference::viewSession/$1');

    $routes->get('networking-center', 'NetworkingCenter::index');

    $routes->get('attendees', 'Attendees::index');
    $routes->get('speakers', 'SpeakersController::index');

    $routes->get('exhibitors', 'ExhibitorsController::index');
    $routes->post('exhibitors/send-message', 'ExhibitorsController::sendMessage');

    $routes->get('sponsors', 'SponsorsController::index');

    $routes->get('points', 'PointsController::index');

    $routes->get('emergence-booth', 'EmergenceBoothController::index');
    $routes->post('emergence/sendSupportEmail', 'EmergenceBoothController::sendSupportEmail');

});

