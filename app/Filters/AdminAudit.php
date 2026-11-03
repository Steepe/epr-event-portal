<?php

namespace App\Filters;

use App\Services\AdminAuditService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAudit implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if (session()->get('is_admin')) {
            $audit = new AdminAuditService();
            $audit->record($audit->eventFor($request), $request, $response);
        }

        return $response;
    }
}
