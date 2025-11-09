<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 00:27
 */

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

/*
 Module-level admin routes.
 Controllers namespace: App\Modules\Admin\Controllers
*/
$routes->group('admin', ['namespace' => 'App\Modules\Admin\Controllers'], function ($routes) {
    // Dashboard
    $routes->get('/', 'DashboardController::index');
    $routes->get('dashboard', 'DashboardController::index');

    // Auth (login/logout)
    $routes->get('login', 'AuthController::login');           // /admin/login
    $routes->post('login', 'AuthController::attempt');
    $routes->get('logout', 'AuthController::logout');

    // Protected group (requires 'adminAuth' filter registered in app/Filters)
    $routes->group('', ['filter' => 'adminAuth'], function ($routes) {
// Attendees Management
        $routes->get('attendees', 'AttendeesController::index');
        $routes->get('attendees/(:num)', 'AttendeesController::view/$1');
        $routes->post('attendees/(:num)/toggle', 'AttendeesController::toggle/$1');

// Edit, Update, and Reset Password
        $routes->post('attendees/(:num)/update', 'AttendeesController::update/$1');
        $routes->post('attendees/(:num)/delete', 'AttendeesController::delete/$1');
        $routes->post('attendees/(:num)/reset', 'AttendeesController::resetPassword/$1');

        // Conferences
        $routes->get('conferences', 'ConferencesController::index');
        $routes->get('conferences/create', 'ConferencesController::create');
        $routes->post('conferences/store', 'ConferencesController::store');
        $routes->get('conferences/(:num)/edit', 'ConferencesController::edit/$1');
        $routes->post('conferences/(:num)/update', 'ConferencesController::update/$1');
        $routes->post('conferences/(:num)/delete', 'ConferencesController::delete/$1');

// Inside the admin group
        $routes->group('conferences', ['namespace' => 'App\Modules\Admin\Controllers'], function ($routes) {
            $routes->get('(:num)/sessions', 'ConferenceSessionsController::index/$1');
            $routes->get('(:num)/sessions/create', 'ConferenceSessionsController::create/$1');
            $routes->post('(:num)/sessions/store', 'ConferenceSessionsController::store/$1');
            $routes->get('sessions/(:num)/edit', 'ConferenceSessionsController::edit/$1');
            $routes->post('sessions/(:num)/update', 'ConferenceSessionsController::update/$1');
            $routes->get('sessions/(:num)/delete', 'ConferenceSessionsController::delete/$1');
        });

        // Speakers
        $routes->group('speakers', function ($routes) {
            $routes->get('/', 'SpeakersController::index');
            $routes->get('create', 'SpeakersController::create');
            $routes->post('store', 'SpeakersController::store');
            $routes->get('(:num)/edit', 'SpeakersController::edit/$1');
            $routes->post('(:num)/update', 'SpeakersController::update/$1');
            $routes->get('(:num)/delete', 'SpeakersController::delete/$1');
        });

        // Exhibitors
        $routes->get('exhibitors', 'ExhibitorsController::index');
        $routes->get('exhibitors/create', 'ExhibitorsController::create');
        $routes->post('exhibitors/store', 'ExhibitorsController::store');
        $routes->get('exhibitors/(:num)/edit', 'ExhibitorsController::edit/$1');
        $routes->post('exhibitors/(:num)/update', 'ExhibitorsController::update/$1');
        $routes->get('exhibitors/(:num)/delete', 'ExhibitorsController::delete/$1');

        // Sponsors
        $routes->get('sponsors', 'SponsorsController::index');
        $routes->get('sponsors/create', 'SponsorsController::create');
        $routes->post('sponsors/store', 'SponsorsController::store');
        $routes->get('sponsors/(:num)/edit', 'SponsorsController::edit/$1');
        $routes->post('sponsors/(:num)/update', 'SponsorsController::update/$1');
        $routes->get('sponsors/(:num)/delete', 'SponsorsController::delete/$1');



        // Messages (admin inbox)
        $routes->get('messages', 'MessagesController::index');
        $routes->get('messages/(:num)', 'MessagesController::view/$1');

        // Payments / reports
        $routes->get('payments', 'PaymentsController::index');
    });
});
