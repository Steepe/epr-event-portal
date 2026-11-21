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

class SpeakersController extends BaseController
{
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $session = session();
        $db = db_connect();
        $conferenceId = (int) $session->get('live-conference-id');
        if ($conferenceId <= 0) {
            $liveConference = $db->table('tbl_conferences')
                ->select('conference_id')
                ->where('status', 'live')
                ->orderBy('start_date', 'DESC')
                ->get(1)
                ->getRowArray();
            $conferenceId = (int) ($liveConference['conference_id'] ?? 0);
        }

        $speakers = [];

        try {
            $speakers = $db->table('tbl_speakers')->orderBy('speaker_name', 'ASC')->get()->getResultArray();
            $speakerIds = array_map('intval', array_column($speakers, 'speaker_id'));
            if ($speakerIds !== []) {
                $sessionQuery = $db->table('tbl_session_speakers ss')
                    ->select('ss.speaker_id, s.sessions_id, s.sessions_name, s.event_date, s.start_time')
                    ->join('tbl_conference_sessions s', 's.sessions_id = ss.sessions_id', 'left')
                    ->whereIn('ss.speaker_id', $speakerIds);
                if ($conferenceId > 0) {
                    $sessionQuery->where('s.conference_id', $conferenceId);
                }
                $sessionsBySpeaker = [];
                foreach ($sessionQuery->orderBy('s.event_date', 'ASC')->orderBy('s.start_time', 'ASC')->get()->getResultArray() as $row) {
                    $speakerId = (int) $row['speaker_id'];
                    unset($row['speaker_id']);
                    $sessionsBySpeaker[$speakerId][] = $row;
                }

                $offersBySpeaker = [];
                foreach ($db->table('tbl_speaker_offers')->whereIn('speaker_id', $speakerIds)->orderBy('id', 'DESC')->get()->getResultArray() as $offer) {
                    $offersBySpeaker[(int) $offer['speaker_id']][] = $offer;
                }

                foreach ($speakers as &$speaker) {
                    $speakerId = (int) $speaker['speaker_id'];
                    $speaker['sessions'] = $sessionsBySpeaker[$speakerId] ?? [];
                    $speaker['offers'] = $offersBySpeaker[$speakerId] ?? [];
                }
                unset($speaker);
            }
        } catch (\Throwable $e) {
            log_message('error', '[SpeakersController] Failed to load speakers: ' . $e->getMessage());
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

        $speakerId = (int) $request->getPost('speaker_id');
        $subject = trim((string) $request->getPost('subject'));
        $messageBody = trim((string) $request->getPost('message'));
        $userId = (int) (session('user_id') ?: session('attendee_id'));
        $db = db_connect();
        $speaker = $db->table('tbl_speakers')->select('speaker_email')->where('speaker_id', $speakerId)->get()->getRowArray();
        $attendee = $db->table('tbl_attendees')->select('firstname, lastname')->where('attendee_id', $userId)->get()->getRowArray();
        $account = $db->table('tbl_users')->select('email')->where('id', $userId)->get()->getRowArray();
        $speakerEmail = trim((string) ($speaker['speaker_email'] ?? ''));
        $senderName = trim((string) (($attendee['firstname'] ?? '') . ' ' . ($attendee['lastname'] ?? ''))) ?: 'Portal attendee';
        $senderEmail = trim((string) ($account['email'] ?? ''));

        if (!filter_var($speakerEmail, FILTER_VALIDATE_EMAIL) || !filter_var($senderEmail, FILTER_VALIDATE_EMAIL) || $subject === '' || $messageBody === '') {
            return redirect()->back()->with('error', 'A valid subject and message are required before contacting this speaker.');
        }

        // FROM your .env no-reply
        $fromEmail = env('email.fromEmail');
        $fromName  = env('email.fromName');

        // Build message
        $emailContent = "
        <p>You have received a new message from an Event Portal attendee.</p>
        <p><strong>Name:</strong> " . esc($senderName) . "</p>
        <p><strong>Email:</strong> " . esc($senderEmail) . "</p>
        <p><strong>Subject:</strong> " . esc($subject) . "</p>
        <p><strong>Message:</strong></p>
        <p>" . nl2br(esc($messageBody)) . "</p>
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
