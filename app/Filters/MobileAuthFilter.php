<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 10:31
 */


namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class MobileAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 🔒 Check login session
        if (!session()->get('isLoggedIn')) {
            // Background requests (chat, forms sent by script) get an error they can show, not a login page.
            if ($request->isAJAX() || str_contains($request->getHeaderLine('Accept'), 'application/json')) {
                return service('response')->setStatusCode(401)->setJSON(['message' => 'Please sign in again.']);
            }

            // Optional: Save intended URL to return after login
            session()->set('returnTo', current_url());

            // Redirect to login
            return redirect()->to(site_url('mobile/login'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-response logic needed
    }
}
