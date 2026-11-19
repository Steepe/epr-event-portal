<?php

namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class ZoomSessionController extends BaseController
{
    /**
     * Dedicated, CSS-isolated page used inside the session-page iframe.
     */
    public function join(int $sessionId)
    {
        $session = $this->authorisedZoomSession($sessionId);
        if ($session === null) {
            return $this->response->setStatusCode(403)->setBody('You do not have access to this session.');
        }

        $sdkVersion = trim((string) env('zoom.sdkVersion'));
        $configured = $this->zoomIsConfigured($sdkVersion);

        return view('App\Modules\Web\Views\zoom_session', [
            'configured'   => $configured,
            'sdkVersion'   => $sdkVersion,
            'signatureUrl' => base_url('attendees/sessions/' . $sessionId . '/zoom/signature'),
            'leaveUrl'     => base_url('attendees/sessions/' . $sessionId . '/zoom/left'),
            'sessionName'  => $session['sessions_name'] ?? 'Zoom session',
        ]);
    }

    /**
     * Lightweight destination for the Meeting SDK after an attendee leaves.
     * This page stays inside the video iframe, preventing the full portal from
     * being rendered recursively inside the session player.
     */
    public function left(int $sessionId)
    {
        $session = $this->authorisedZoomSession($sessionId);
        if ($session === null) {
            return $this->response->setStatusCode(403)->setBody('You do not have access to this session.');
        }

        return view('App\Modules\Web\Views\zoom_session_left', [
            'sessionName' => $session['sessions_name'] ?? 'Zoom session',
            'rejoinUrl'   => base_url('attendees/sessions/' . $sessionId . '/zoom'),
        ]);
    }

    /**
     * Returns a short-lived Meeting SDK signature only after portal access is
     * verified. The Meeting SDK Client Secret never reaches the browser.
     */
    public function signature(int $sessionId)
    {
        $session = $this->authorisedZoomSession($sessionId);
        if ($session === null) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'You do not have access to this session.']);
        }

        $sdkVersion = trim((string) env('zoom.sdkVersion'));
        if (! $this->zoomIsConfigured($sdkVersion)) {
            return $this->response->setStatusCode(503)->setJSON(['message' => 'Zoom has not been configured for this portal yet.']);
        }

        $meetingNumber = (string) ($session['zoom_meeting_number'] ?? '');
        $now = time();
        $payload = [
            'appKey'   => trim((string) env('zoom.clientId')),
            'mn'       => $meetingNumber,
            'role'     => 0,
            'iat'      => $now,
            'exp'      => $now + 7200,
            'tokenExp' => $now + 7200,
        ];

        return $this->response
            ->setHeader('Cache-Control', 'no-store, private')
            ->setJSON([
                'signature'     => $this->sign($payload, trim((string) env('zoom.clientSecret'))),
                'sdkKey'        => $payload['appKey'],
                'meetingNumber' => $meetingNumber,
                'passWord'      => (string) ($session['zoom_passcode'] ?? ''),
                'userName'      => $this->attendeeName(),
                'userEmail'     => (string) session('email'),
                'isWebinar'     => (bool) ($session['zoom_is_webinar'] ?? false),
            ]);
    }

    private function authorisedZoomSession(int $sessionId): ?array
    {
        if (! session('logged_in')) {
            return null;
        }

        $session = db_connect()->table('tbl_conference_sessions')
            ->where('sessions_id', $sessionId)
            ->get()
            ->getRowArray();

        if (! $session) {
            throw new PageNotFoundException('Session not found.');
        }

        $canAccess = (session('plan') ?? 1) >= ($session['access_level'] ?? 1);
        $meetingNumber = (string) ($session['zoom_meeting_number'] ?? '');
        if (! $canAccess || ($session['stream_provider'] ?? 'livepush') !== 'zoom' || ! preg_match('/^\d{9,16}$/', $meetingNumber)) {
            return null;
        }

        return $session;
    }

    private function attendeeName(): string
    {
        $name = trim((string) session('firstname') . ' ' . (string) session('lastname'));

        return $name !== '' ? $name : 'EPR Global Attendee';
    }

    private function zoomIsConfigured(string $sdkVersion): bool
    {
        return trim((string) env('zoom.clientId')) !== ''
            && trim((string) env('zoom.clientSecret')) !== ''
            && (bool) preg_match('/^\d+\.\d+\.\d+$/', $sdkVersion);
    }

    private function sign(array $payload, string $secret): string
    {
        $header = $this->base64UrlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT'], JSON_THROW_ON_ERROR));
        $body = $this->base64UrlEncode(json_encode($payload, JSON_THROW_ON_ERROR));
        $signature = hash_hmac('sha256', $header . '.' . $body, $secret, true);

        return $header . '.' . $body . '.' . $this->base64UrlEncode($signature);
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
