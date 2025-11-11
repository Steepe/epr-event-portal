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

        $speakers = [];

        try {
            $response = $curl->get("{$apiBase}/speakers", [
                'headers' => ['X-API-KEY' => $apiKey],
            ]);

            $body = json_decode($response->getBody(), true);

            if (isset($body['status']) && $body['status'] === 'success') {
                $speakers = $body['data'] ?? [];
            } else {
                log_message('warning', '[SpeakersController] API returned error or invalid format: ' . json_encode($body));
            }

        } catch (\Throwable $e) {
            // 🧩 Log the error but don’t redirect
            log_message('error', '[SpeakersController] Failed to load speakers: ' . $e->getMessage());
        }

        // ✅ Always render the page, even if speakers array is empty
        $data = [
            'speakers'   => $speakers,
            'page_title' => 'Speakers'
        ];

        return module_view('Web', 'speakers', $data);
    }

}
