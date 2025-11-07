<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 31/10/2025
 * Time: 21:40
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class SponsorsController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $apiKey  = env('api.securityKey');
        $apiBase = rtrim(base_url('api'), '/');

        $curl = service('curlrequest', ['verify' => false]);

        try {
            $response = $curl->get("{$apiBase}/sponsors", [
                'headers' => ['X-API-KEY' => $apiKey],
            ]);

            $body = json_decode($response->getBody(), true);
            $sponsors = $body['data'] ?? [];

            return module_view('Web', 'sponsors', ['sponsors' => $sponsors]);
        } catch (\Throwable $e) {
            log_message('error', 'Failed to load sponsors: ' . $e->getMessage());
            return module_view('Web', 'sponsors', ['sponsors' => []]);
        }
    }
}
