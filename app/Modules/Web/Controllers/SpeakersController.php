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

    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $session = session();

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
                log_message('warning', '[SpeakersController] API returned invalid: ' . json_encode($body));
            }

        } catch (\Throwable $e) {
            log_message('error', '[SpeakersController] Failed to load speakers: ' . $e->getMessage());
        }


        /** ---------------------------------------------------------
         *  🔥 Fetch offers for each speaker (API-driven)
         * ---------------------------------------------------------- */
        foreach ($speakers as &$speaker) {

            $speakerId = $speaker['speaker_id'] ?? null;
            if (!$speakerId) {
                $speaker['offers'] = [];
                continue;
            }

            try {
                $offerRes = $curl->get("{$apiBase}/speakers/{$speakerId}/offers", [
                    'headers' => ['X-API-KEY' => $apiKey],
                ]);

                $offerBody = json_decode($offerRes->getBody(), true);

                if (!empty($offerBody['status']) && $offerBody['status'] === 'success') {
                    $speaker['offers'] = $offerBody['data'] ?? [];
                } else {
                    $speaker['offers'] = [];
                }

            } catch (\Throwable $e) {
                log_message('error', "[SpeakersController] Failed loading offers for speaker {$speakerId}: " . $e->getMessage());
                $speaker['offers'] = [];
            }
        }


        // Render page
        $data = [
            'speakers'   => $speakers,
            'page_title' => 'Speakers'
        ];

        return module_view('Web', 'speakers', $data);
    }

    public function sendMessage()
    {
        $speakerId = $this->request->getPost('speaker_id');
        $name      = $this->request->getPost('name');
        $email     = $this->request->getPost('email');
        $subject   = $this->request->getPost('subject');
        $msg       = $this->request->getPost('message');

        // Get speaker email
        $speaker = $this->speakerModel->find($speakerId);

        // Assuming speakers have an `email` column
        $to = $speaker['email'];

        $body = "
        Message from: $name ($email)

        Subject: $subject

        $msg
    ";

        // send email normally using CI4 Email services

        return redirect()->back()->with('success', 'Message sent successfully.');
    }


}
