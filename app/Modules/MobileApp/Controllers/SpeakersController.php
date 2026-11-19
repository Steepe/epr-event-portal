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

    /**
     * The standalone list now lives inside the Networking Center.
     */
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        return redirect()->to(site_url('mobile/networking-center?tab=speakers'));
    }

    /** Send a speaker enquiry using the signed-in attendee's server-side identity. */
    public function sendMessage()
    {
        $payload = $this->request->getJSON(true) ?: $this->request->getPost();
        $speakerId = (int) ($payload['speaker_id'] ?? 0);
        $subject = trim((string) ($payload['subject'] ?? ''));
        $message = trim((string) ($payload['message'] ?? ''));
        $userId = (int) (session('user_id') ?: session('attendee_id'));

        if ($speakerId <= 0 || $subject === '' || $message === '' || mb_strlen($subject) > 255 || mb_strlen($message) > 5000 || $userId <= 0) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 'error', 'message' => 'Enter a subject and message before contacting this speaker.']);
        }

        $db = db_connect();
        $speaker = $db->table('tbl_speakers')->select('speaker_name, speaker_email')->where('speaker_id', $speakerId)->get()->getRowArray();
        $attendee = $db->table('tbl_attendees')->select('firstname, lastname')->where('attendee_id', $userId)->get()->getRowArray();
        $account = $db->table('tbl_users')->select('email')->where('id', $userId)->get()->getRowArray();
        $speakerEmail = trim((string) ($speaker['speaker_email'] ?? ''));
        $senderEmail = trim((string) ($account['email'] ?? ''));
        $senderName = trim((string) (($attendee['firstname'] ?? '') . ' ' . ($attendee['lastname'] ?? ''))) ?: 'Portal attendee';

        if (! filter_var($speakerEmail, FILTER_VALIDATE_EMAIL) || ! filter_var($senderEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 'error', 'message' => 'This speaker cannot be contacted by email right now.']);
        }

        $email = service('email');
        $email->setFrom((string) env('email.fromEmail'), (string) env('email.fromName'));
        $email->setTo($speakerEmail);
        $email->setReplyTo($senderEmail, $senderName);
        $email->setSubject('Message from attendee: ' . $subject);
        $email->setMailType('html');
        $email->setMessage('<p>You have received a new message from an Event Portal attendee.</p><p><strong>Name:</strong> ' . esc($senderName) . '</p><p><strong>Email:</strong> ' . esc($senderEmail) . '</p><p><strong>Subject:</strong> ' . esc($subject) . '</p><p><strong>Message:</strong></p><p>' . nl2br(esc($message)) . '</p>');

        if (! $email->send()) {
            log_message('error', 'Mobile speaker message failed: {debug}', ['debug' => $email->printDebugger(['headers'])]);
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Message could not be sent. Please try again.']);
        }

        return $this->response->setJSON(['status' => 'success']);
    }
}
