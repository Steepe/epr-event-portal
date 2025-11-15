<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 09:54
 */

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Modules\MobileApp\Models\SpeakersModel;

class SpeakersController extends BaseController
{
    protected SpeakersModel $speakersModel;

    public function __construct()
    {
        $this->speakersModel = new SpeakersModel();
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        $speakers = $this->speakersModel->getAllSpeakers();

        // 🔥 Attach offers to each speaker
        $db = db_connect();

        foreach ($speakers as &$sp) {
            $sp['offers'] = $db->table('tbl_speaker_offers')
                ->where('speaker_id', $sp['speaker_id'])
                ->orderBy('id', 'DESC')
                ->get()
                ->getResultArray();
        }

        $data = [
            'page_title' => 'Speakers',
            'speakers'   => $speakers
        ];

        return module_view('MobileApp', 'speakers_list', $data);
    }

    public function sendMessage()
    {
        $speakerId    = $this->request->getPost('speaker_id');
        $speakerEmail = $this->request->getPost('speaker_email');
        $name         = $this->request->getPost('name');
        $email        = $this->request->getPost('email');
        $subject      = $this->request->getPost('subject');
        $message      = $this->request->getPost('message');

        if (!$speakerEmail) {
            return redirect()->back()->with('error', 'Speaker email not found.');
        }

        $emailService = \Config\Services::email();

        $emailService->setFrom(
            getenv('mail.fromAddress'),
            getenv('mail.fromName')
        );

        // Reply-to attendee
        $emailService->setReplyTo($email, $name);

        // Send to speaker
        $emailService->setTo($speakerEmail);

        $emailService->setSubject("Message from Attendee: " . $subject);

        $body = "
        <h3>You received a message from an event attendee</h3>
        <p><strong>Name:</strong> {$name}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Subject:</strong> {$subject}</p>
        <p><strong>Message:</strong></p>
        <p>{$message}</p>
    ";

        $emailService->setMessage($body);

        if ($emailService->send()) {
            return redirect()->back()->with('success', 'Message sent successfully.');
        }

        return redirect()->back()->with('error', 'Message failed to send.');
    }

}
