<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Sends phones that open a desktop attendee page to the matching mobile screen.
 *
 * Only page views (GET) are redirected. Checkout, payment webhooks, presence pings and
 * anything without a mobile equivalent stay where they are.
 */
class MobileRedirect implements FilterInterface
{
    /** Same devices the desktop pages already redirect in the browser, plus other phone browsers. */
    private const PHONE_PATTERN = '/android|iphone|ipod|kindle|windows phone|iemobile|blackberry|bb10|opera mini|webos/i';

    /** Desktop path => mobile path. */
    private const PAGES = [
        'attendees/login'              => 'mobile/login',
        'attendees/register'           => 'mobile/register',
        'attendees/forgot-password'    => 'mobile/forgot-password',
        'attendees/reset-password'     => 'mobile/reset-password',
        'attendees/start'              => 'mobile/welcome',
        'attendees/home'               => 'mobile/home',
        'attendees/lobby'              => 'mobile/lobby',
        'attendees/networking_center'  => 'mobile/networking-center',
        'attendees/attendees'          => 'mobile/networking-center',
        'attendees/speakers'           => 'mobile/networking-center?tab=speakers',
        'attendees/communities'        => 'mobile/networking-center?tab=communities',
        'attendees/sponsors'           => 'mobile/sponsors',
        'attendees/exhibitors'         => 'mobile/exhibitors',
        'attendees/emergence_booth'    => 'mobile/emergence-booth',
        'attendees/envision'           => 'mobile/envision',
        'attendees/webinars'           => 'mobile/webinars',
        'attendees/points'             => 'mobile/points',
        'attendees/profile'            => 'mobile/profile',
        'attendees/notifications'      => 'mobile/notifications',
        'attendees/past_conferences'   => 'mobile/past-conferences',
        'attendees/payments/success'   => 'mobile/payment/success',
        'attendees/payments/cancelled' => 'mobile/payment/cancelled',
    ];

    /** Desktop path pattern => mobile path, for pages that carry an id or token. */
    private const PATTERNS = [
        '#^attendees/agenda/(\d+)$#'               => 'mobile/agenda/$1',
        '#^attendees/sessions/(\d+)$#'             => 'mobile/session/$1',
        '#^attendees/pastConference/(\d+)$#'       => 'mobile/pastConference/$1',
        '#^attendees/exhibitors/\d+$#'             => 'mobile/exhibitors',
        '#^attendees/upgrade/\d+$#'                => 'mobile/upgrade',
        '#^attendees/reset-password/([A-Za-z0-9]+)$#' => 'mobile/reset-password/$1',
    ];

    public static function isPhone(RequestInterface $request): bool
    {
        $agent = $request->getHeaderLine('User-Agent');

        return $agent !== '' && preg_match(self::PHONE_PATTERN, $agent) === 1;
    }

    /**
     * The mobile path for a desktop path, or null when the page has no mobile equivalent.
     */
    public static function mobilePathFor(string $path): ?string
    {
        $path = trim($path, '/');

        if (isset(self::PAGES[$path])) {
            return self::PAGES[$path];
        }

        foreach (self::PATTERNS as $pattern => $target) {
            if (preg_match($pattern, $path) === 1) {
                return preg_replace($pattern, $target, $path);
            }
        }

        return null;
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        if (! in_array(strtolower($request->getMethod()), ['get', 'head'], true) || ! self::isPhone($request)) {
            return null;
        }

        $target = self::mobilePathFor($request->getUri()->getPath());

        if ($target === null) {
            return null;
        }

        // Keep reset tokens, payment references and similar query values.
        $query = $request->getUri()->getQuery();
        if ($query !== '') {
            $target .= (str_contains($target, '?') ? '&' : '?') . $query;
        }

        return redirect()->to(site_url($target))->setHeader('Vary', 'User-Agent');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do after the response.
    }
}
