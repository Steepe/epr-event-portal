<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 20/11/2025
 * Time: 21:17
 */

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Modules\Api\Models\TblUsersModel;

class PremiumAccess implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userId = session()->get('user_id') ?? $request->getHeaderLine('X-User-Id');
        if (!$userId) {
            return redirect()->to('/attendees/login');
        }
        $m = new TblUsersModel();
        $user = $m->find($userId);
        if (!$user || empty($user['premium_access'])) {
            // redirect to upgrade page
            return redirect()->to('/payments/upgrade');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
