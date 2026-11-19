<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 05:40
 */


namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Modules\MobileApp\Models\SessionModel;
use App\Modules\MobileApp\Models\SpeakersModel;

class Agenda extends BaseController
{
    public function index($conference_id = null): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        if (empty($conference_id)) {
            return redirect()->to(site_url('mobile/home'));
        }

        $sessionModel  = new SessionModel();
        $speakerModel  = new SpeakersModel();

        // Fetch all sessions
        $sessions = $sessionModel
            ->where('conference_id', $conference_id)
            ->orderBy('event_date', 'ASC')
            ->orderBy('start_time', 'ASC')
            ->findAll();

        // Attach speakers
        foreach ($sessions as &$session) {
            $session['speakers'] = $speakerModel->getSpeakersBySession($session['sessions_id']);
        }

        unset($session);

        $db   = db_connect();
        $user = $db->table('tbl_users')->where('id', session('user_id'))->get()->getRowArray();

        $conference = $db->table('tbl_conferences')
            ->where('conference_id', $conference_id)
            ->get()->getRowArray();

        $dateRange = $this->sessionDateRange($conference ?? []);
        if ($dateRange !== null) {
            $sessions = array_values(array_filter($sessions, static function (array $session) use ($dateRange): bool {
                $date = (string) ($session['event_date'] ?? '');

                return $date >= $dateRange['start'] && $date <= $dateRange['end'];
            }));
        }

        return module_view('MobileApp', 'agenda', [
            'sessions'       => $sessions,
            'conference_id'  => $conference_id,
            'conference'     => $conference,
            'premium'        => (int) ($user['premium_access'] ?? 0),
        ]);
    }

    /** @return array{start: string, end: string}|null */
    private function sessionDateRange(array $conference): ?array
    {
        $days = max(0, (int) ($conference['days'] ?? 0));
        $startDate = substr(trim((string) ($conference['start_date'] ?? '')), 0, 10);
        if ($days < 1 || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            return null;
        }

        try {
            $start = new \DateTimeImmutable($startDate, new \DateTimeZone('UTC'));
        } catch (\Exception) {
            return null;
        }

        return [
            'start' => $start->format('Y-m-d'),
            'end' => $start->modify('+' . ($days - 1) . ' days')->format('Y-m-d'),
        ];
    }
}
