<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 17:48
 */


namespace App\Modules\Web\Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('attendees', ['namespace' => 'App\Modules\Web\Controllers'], function ($routes) {
    $routes->get('register', 'RegistrationController::index');
    $routes->get('login', 'LoginController::index');
    $routes->post('logout', 'LoginController::logout');
});


$routes->group('attendees', ['namespace' => 'App\Modules\Web\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('home', 'HomeController::index');
    $routes->get('lobby', 'LobbyController::index');
    $routes->get('agenda', 'AgendaController::index');
    $routes->get('sessions/(:num)', 'SessionsController::view/$1'); // 👈 single session page

    $routes->get('networking_center', 'NetworkingCenterController::index');
    $routes->get('attendees', 'AttendeesController::index');          // Attendees listing

    // Optional: endpoints for messaging via AJAX
    $routes->post('messages/submit_direct_message', 'MessagesController::submitDirectMessage');

    $routes->get('speakers', 'SpeakersController::index');

    $routes->get('past_conferences', 'PastConferencesController::index');
    $routes->get('sponsors', 'SponsorsController::index');

    $routes->get('exhibitors', 'ExhibitorsController::index');
    $routes->get('exhibitors/(:num)', 'ExhibitorsController::booth/$1'); // "Enter Booth" page

    $routes->get('emergence_booth', 'EmergenceBoothController::index');

});
