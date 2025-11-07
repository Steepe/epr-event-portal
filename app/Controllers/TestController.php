<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 03/11/2025
 * Time: 23:40
 */

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class TestController extends Controller
{
    public function redis(): \CodeIgniter\HTTP\ResponseInterface
    {
        $cache = Services::cache();

        $success = $cache->save('greeting', 'Hello, Mr. Steepe — Redis is working perfectly!', 300);
        $value = $cache->get('greeting');

        return $this->response->setJSON([
            'status' => $success ? 'ok' : 'fail',
            'message' => $value ?? 'No value found'
        ]);
    }
}
