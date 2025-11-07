<?php
/**
 * SessionsController
 * Handles attendee session detail view
 *
 * Author: Oluwamayowa Steepe
 * Project: epr-event-portal
 * Date: 29/10/2025
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class SessionsController extends BaseController
{
    use ResponseTrait;

    public function view($sessionId): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('attendees/login'));
        }

        $apiKey  = env('api.securityKey');
        $apiBase = rtrim(base_url('api'), '/');
        $curl    = service('curlrequest', ['verify' => false]); // self-signed SSL safe

        try {
            $response = $curl->get("{$apiBase}/sessions/view/{$sessionId}", [
                'headers' => ['X-API-KEY' => $apiKey],
            ]);

            $body = json_decode($response->getBody(), true);

            // ✅ Flexible handling of both formats
            $dataBlock = $body['data'] ?? [];
            $sessionData = $dataBlock['session'] ?? $dataBlock; // handle if "session" key doesn’t exist
            $speakers    = $dataBlock['speakers'] ?? [];
            $sponsors    = $dataBlock['sponsors'] ?? [];

            if (empty($sessionData)) {
                return redirect()
                    ->to(base_url('attendees/agenda'))
                    ->with('error', 'Session not found.');
            }

            $data = [
                'session'   => $sessionData,
                'speakers'  => $speakers,
                'sponsors'  => $sponsors,
                'timezone'  => $session->get('user_timezone') ?? 'Africa/Lagos',
                'page_title'=> $sessionData['sessions_name'] ?? 'Session Details'
            ];

            return module_view('Web', 'session_detail', $data);

        } catch (\Throwable $e) {
            log_message('critical', '[SessionsController] Error fetching session '.$sessionId.': '.$e->getMessage());
            return redirect()
                ->to(base_url('attendees/agenda'))
                ->with('error', 'Unable to load session at this time.');
        }
    }
}
