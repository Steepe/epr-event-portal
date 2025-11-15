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
        $request = service('request');
        $emailService = service('email');

        $speakerId      = $request->getPost('speaker_id');
        $speakerEmail   = $request->getPost('speaker_email');
        $senderName     = $request->getPost('name');
        $senderEmail    = $request->getPost('email');
        $subject        = $request->getPost('subject');
        $messageBody    = $request->getPost('message');

        if (!$speakerEmail || !$senderEmail || !$subject || !$messageBody) {
            return redirect()->back()->with('error', 'All fields are required.');
        }

        // FROM your .env no-reply
        $fromEmail = env('email.fromEmail');
        $fromName  = env('email.fromName');

        // Build message
        $emailContent = "
        <p>You have received a new message from an Event Portal attendee.</p>
        <p><strong>Name:</strong> {$senderName}</p>
        <p><strong>Email:</strong> {$senderEmail}</p>
        <p><strong>Subject:</strong> {$subject}</p>
        <p><strong>Message:</strong></p>
        <p>{$messageBody}</p>
    ";

        // Configure email
        $emailService->setFrom($fromEmail, $fromName);
        $emailService->setTo($speakerEmail);
        $emailService->setReplyTo($senderEmail, $senderName);
        $emailService->setSubject("Message from attendee: {$subject}");
        $emailService->setMessage($emailContent);
        $emailService->setMailType('html');

        if (!$emailService->send()) {
            log_message('error', 'Email failed: ' . $emailService->printDebugger(['headers']));
            return redirect()->back()->with('error', 'Message could not be sent.');
        }

        return redirect()->back()->with('success', 'Your message has been sent to the speaker.');
    }


}
