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
    $routes->get('forgot-password', 'Auth::forgotPassword');
    $routes->get('reset-password', 'Auth::resetPassword');
    $routes->get('reset-password/(:any)', 'Auth::resetPassword/$1');

    // Where the payment gateways send phones back to
    $routes->get('payment/success', 'PaymentResult::success');
    $routes->get('payment/cancelled', 'PaymentResult::cancelled');
});


// The filter has to sit in the same options array as the namespace: a second array is ignored.
$routes->group('mobile', ['namespace' => 'App\Modules\MobileApp\Controllers', 'filter' => 'mobileauth'], function ($routes) {
    $routes->get('welcome', 'Welcome::index');
    $routes->get('home', 'Home::index');
    $routes->get('lobby', 'Lobby::index');
    $routes->get('agenda/(:num)', 'Agenda::index/$1');
    $routes->get('session/(:num)', 'Session::detail/$1');
    $routes->get('webinars', 'Webinars::index');
    $routes->get('envision', 'Home::envision');

    $routes->get('past-conferences', 'PastConference::archive');
    $routes->get('pastConference/(:num)', 'PastConference::index/$1');
    $routes->get('pastConference/session/(:num)', 'PastConference::viewSession/$1');

    $routes->get('networking-center', 'NetworkingCenter::index');

    // Country communities live in the Networking Center; manager chat reuses the desktop portal handlers
    $routes->get('communities', 'NetworkingCenter::communitiesTab');
    $routes->get('communities/chat/(:num)', '\App\Modules\Web\Controllers\CommunityController::chatMessages/$1');
    $routes->post('communities/chat/(:num)', '\App\Modules\Web\Controllers\CommunityController::sendChat/$1');

    $routes->get('attendees', 'Attendees::index');
    $routes->get('speakers', 'SpeakersController::index');

    $routes->get('exhibitors', 'ExhibitorsController::index');
    $routes->post('exhibitors/send-message', 'ExhibitorsController::sendMessage');

    $routes->get('sponsors', 'SponsorsController::index');

    $routes->get('points', 'PointsController::index');

    $routes->get('emergence-booth', 'EmergenceBoothController::index');
    $routes->post('emergence/sendSupportEmail', 'EmergenceBoothController::sendSupportEmail');
    // Booth callback + team chat reuse the desktop portal handlers
    $routes->post('emergence-booth/callback', '\App\Modules\Web\Controllers\EmergenceBoothController::callback');
    $routes->get('emergence-booth/chat/(:num)', '\App\Modules\Web\Controllers\EmergenceBoothController::chatMessages/$1');
    $routes->post('emergence-booth/chat/(:num)', '\App\Modules\Web\Controllers\EmergenceBoothController::sendChat/$1');

    $routes->get('explore', 'Explore::index');
    $routes->get('notifications', 'Notifications::index');
    $routes->get('upgrade', 'Upgrade::index');

    $routes->get('profile', 'Profile::index');
    $routes->post('profile/update', 'Profile::update');
    $routes->post('profile/upload-photo', 'Profile::uploadPhoto');

});

