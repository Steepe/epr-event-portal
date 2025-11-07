<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 31/10/2025
 * Time: 00:08
 */

namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class AttendeesController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('attendees/login'));
        }

        // 🔗 Fetch attendees from API
        $apiKey  = env('api.securityKey');
        $apiBase = rtrim(base_url('api'), '/');
        $curl    = service('curlrequest', ['verify' => false]);

        $query = $this->request->getGet('sort');
        $search = $this->request->getGet('attendee_name');

        $params = [];
        if ($query) $params['sort'] = $query;
        if ($search) $params['attendee_name'] = $search;

        try {
            $response = $curl->get("{$apiBase}/attendees", [
                'headers' => ['X-API-KEY' => $apiKey],
                'query'   => $params
            ]);

            $body = json_decode($response->getBody(), true);
            $data = [
                'attendees' => $body['data']['attendees'] ?? [],
                'alphabet_count' => $body['data']['alphabet_count'] ?? [],
                'page_title' => 'Attendees',
            ];
        } catch (\Throwable $e) {
            log_message('error', '[AttendeesController] '.$e->getMessage());
            $data = [
                'attendees' => [],
                'alphabet_count' => [],
                'page_title' => 'Attendees',
            ];
        }

        return module_view('Web', 'attendees_list', $data);
    }
}
