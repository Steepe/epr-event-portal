<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 09:55
 */


use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('api', [
    'namespace' => 'App\Modules\Api\Controllers',
    'filter'    => 'apiauth', // ✅ unified filter
], function ($routes) {

    // 🔐 Authentication
    $routes->post('login', 'LoginController::login');
    $routes->post('logout', 'LoginController::logout');
    $routes->post('register', 'RegistrationController::register');

    // 🌍 Countries
    $routes->get('countries', 'CountriesController::index');

    // 🎟️ Ticket Prices
    $routes->get('ticket-prices/(:num)/(:segment)', 'TicketPricesController::getPrice/$1/$2');
    $routes->get('ticket-prices', 'TicketPricesController::index');
    $routes->get('ticket-prices/(:num)', 'TicketPricesController::show/$1');
    $routes->post('ticket-prices', 'TicketPricesController::create');
    $routes->put('ticket-prices/(:num)', 'TicketPricesController::update/$1');
    $routes->delete('ticket-prices/(:num)', 'TicketPricesController::delete/$1');

    // 💳 Payments
    $routes->get('payments/check/(:num)', 'PaymentsController::check/$1');

    // 🎤 Conferences
    $routes->get('conferences', 'ConferenceController::index');
    $routes->post('conferences', 'ConferenceController::create');
    $routes->get('conferences/live', 'ConferenceController::live');
    $routes->get('conferences/past', 'ConferenceController::past');
    $routes->get('conferences/(:segment)', 'ConferenceController::show/$1');

    // 📅 Sessions
    $routes->get('sessions/(:num)', 'ConferenceSessionsController::index/$1');
    $routes->get('sessions/view/(:num)', 'ConferenceSessionsController::show/$1');

    // 👤 Users
    $routes->get('users/(:num)', 'UsersController::show/$1');
    $routes->get('attendee-sessions/(:num)', 'UsersController::attendeeSessions/$1');

    // 👥 Attendees
    $routes->get('attendees', 'AttendeesController::index');
    $routes->get('attendees/(:num)', 'AttendeesController::show/$1');

    // 🎙️ Speakers
    $routes->get('speakers', 'SpeakersController::index');

    $routes->group('speakers', function ($routes) {
        $routes->get('/', 'SessionSpeakersController::index');
        $routes->get('session/(:num)', 'SessionSpeakersController::bySession/$1');
    });

    // 💼 Sponsors
    $routes->get('sponsors', 'SponsorsController::index');

    // 🏢 Exhibitors
    $routes->get('exhibitors', 'ExhibitorsController::index');
    $routes->post('exhibitors/(:num)/message', 'ExhibitorsController::sendMessage/$1');

    // 💬 Messages
    $routes->post('messages/send', 'MessagesController::send');
    $routes->get('messages/inbox/(:num)', 'MessagesController::inbox/$1');
    $routes->get('messages/outbox/(:num)', 'MessagesController::outbox/$1');
    $routes->get('messages/unread/(:num)', 'Messages::unread/$1');


    $routes->get('conferences/live', 'ConferenceController::live');
    $routes->get('conferences/live/sessions', 'ConferenceController::liveSessions');
    $routes->get('conferences/(:num)/sessions', 'ConferenceController::sessionsByConference/$1');

});
