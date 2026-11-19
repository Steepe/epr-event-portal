<?php

namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use App\Services\PortalAccessService;

class PlatformTourController extends BaseController
{
    private const HAKIMA_VIDEO_URL = 'https://vz-76fcf8a2-891.b-cdn.net/49acf82c-ccc1-415f-8bd4-1363cceaa6a3/original';

    public function index(): \CodeIgniter\HTTP\RedirectResponse
    {
        return redirect()->to(site_url('attendees/home'));
    }

    public function home(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (! session('logged_in')) {
            return redirect()->to(site_url('attendees/login'));
        }

        $conference = (new PortalAccessService())->liveConference();
        if (! $conference) {
            return redirect()->to(site_url('attendees/start'));
        }

        $conferenceId = (int) $conference['conference_id'];

        return module_view('Web', 'platform_tour', [
            'conference' => $conference,
            'hakima_video_url' => self::HAKIMA_VIDEO_URL,
            'tour_cues' => [
                ['at' => 0, 'title' => 'Welcome', 'caption' => 'Welcome to EPR Global’s Accelerate 2026.', 'url' => site_url('attendees/start')],
                ['at' => 27.85, 'title' => 'Entrance', 'caption' => 'You are at the entrance to Accelerate 2026.', 'url' => site_url('attendees/start')],
                ['at' => 33.72, 'title' => 'Lobby', 'caption' => 'Click Lobby to enter your central space for sessions, people, exhibitors, sponsors, and the Emergence Booth.', 'url' => site_url('attendees/lobby')],
                ['at' => 55.81, 'title' => 'Live Conference', 'caption' => 'Browse live sessions and join the discussion. Session times adjust to your local time zone.', 'url' => site_url('attendees/agenda/' . $conferenceId) . '?day=2026-11-21'],
                ['at' => 83.23, 'title' => 'Networking Center', 'caption' => 'Connect with fellow attendees and discover the speakers.', 'url' => site_url('attendees/networking_center')],
                ['at' => 88.79, 'title' => 'Attendees', 'caption' => 'Browse participant profiles and connect with people who share your interests.', 'url' => site_url('attendees/attendees')],
                ['at' => 94.56, 'title' => 'Speakers', 'caption' => 'Explore speaker biographies, expertise, and the sessions they are involved in.', 'url' => site_url('attendees/speakers')],
                ['at' => 107.80, 'title' => 'Exhibitors', 'caption' => 'Enter an exhibitor booth to discover their work and engagement options.', 'url' => site_url('attendees/exhibitors'), 'zoom' => 0.74],
                ['at' => 135.35, 'title' => 'Sponsors', 'caption' => 'Meet the organisations supporting Accelerate 2026.', 'url' => site_url('attendees/sponsors')],
                ['at' => 161.98, 'title' => 'Emergence Booth', 'caption' => 'Meet the EPR Global team, explore programmes, and access the Take-Home Toolkit.', 'url' => site_url('attendees/emergence_booth'), 'zoom' => 0.84],
                ['at' => 176.32, 'title' => 'Request a callback', 'caption' => 'Leave your card and tell the EPR Global team what you would like to discuss.', 'url' => site_url('attendees/emergence_booth') . '#callback-request', 'zoom' => 0.84],
                ['at' => 193.15, 'title' => 'Notifications', 'caption' => 'Watch the notification bell for announcements, reminders, and programme updates.', 'url' => site_url('attendees/notifications'), 'highlight' => 'notifications'],
                ['at' => 208, 'title' => 'Profile', 'caption' => 'Access your account information and manage your personal details.', 'url' => site_url('attendees/profile')],
                ['at' => 215.93, 'title' => 'Helpdesk & Technical Support', 'caption' => 'Visit the Emergence Booth whenever you need help or technical support.', 'url' => site_url('attendees/emergence_booth')],
            ],
        ]);
    }
}
