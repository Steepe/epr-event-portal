<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 10:37
 */

namespace App\Modules\Auth\Config;

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->group('api/auth', ['namespace' => 'App\Modules\Auth\Controllers'], static function ($routes) {
    $routes->post('login', 'LoginController::login');
    $routes->post('logout', 'LoginController::logout');
    $routes->post('sso-login', 'LoginController::ssoLogin'); // SSO endpoint — pluggable
    $routes->post('set-sessions', 'LoginController::setAdditionalSessions');
});
