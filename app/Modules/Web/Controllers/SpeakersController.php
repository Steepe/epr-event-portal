<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 31/10/2025
 * Time: 06:41
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class SpeakersController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('attendees/login'));
        }

        $apiKey  = env('api.securityKey');
        $apiBase = rtrim(base_url('api'), '/');
        $curl    = service('curlrequest', ['verify' => false]);

        try {
            $response = $curl->get("{$apiBase}/speakers", [
                'headers' => ['X-API-KEY' => $apiKey],
            ]);

            $body = json_decode($response->getBody(), true);

            $data = [
                'speakers'   => $body['data'] ?? [],
                'page_title' => 'Speakers'
            ];

            return module_view('Web', 'speakers', $data);

        } catch (\Throwable $e) {
            log_message('error', 'Failed to load speakers: ' . $e->getMessage());
            return redirect()->to(base_url('attendees/networking-center'))
                ->with('error', 'Unable to load speakers at this time.');
        }
    }
}
