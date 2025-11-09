<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 17:10
 */

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null): ResponseInterface|RequestInterface|string|\CodeIgniter\HTTP\RedirectResponse|null
    {
        $session = session();
        $user = $session->get('admin'); // your logged-in admin user array

        if (!$user || !in_array($user['role'], (array) $arguments)) {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Access denied. Superadmin only.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do after
    }
}
