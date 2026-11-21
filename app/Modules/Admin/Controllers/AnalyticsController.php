<?php

namespace App\Modules\Admin\Controllers;

use App\Controllers\BaseController;

class AnalyticsController extends BaseController
{
    public function index(): string
    {
        $db = db_connect();
        $count = static fn (string $table): int => $db->tableExists($table) ? (int) $db->table($table)->countAllResults() : 0;
        $users = $count('tbl_users');
        $attendees = $count('tbl_attendees');
        $registered2026 = $db->tableExists('tbl_users') && $db->fieldExists('registered_2026_at', 'tbl_users')
            ? (int) $db->table('tbl_users')->where('registered_2026_at IS NOT NULL', null, false)->countAllResults()
            : 0;
        $ratings = $count('tbl_session_ratings');
        $averageRating = $ratings && $db->tableExists('tbl_session_ratings') ? (float) ($db->table('tbl_session_ratings')->selectAvg('rating')->get()->getRowArray()['rating'] ?? 0) : 0;
        $uniqueSessionViewers = $count('tbl_session_viewers');

        $countries = $db->tableExists('tbl_attendees') ? $db->table('tbl_attendees')->select('country, COUNT(*) AS total')
            ->where('country IS NOT NULL', null, false)->where("TRIM(country) <> ''", null, false)->groupBy('country')->orderBy('total', 'DESC')->limit(12)->get()->getResultArray() : [];
        $sessions = ($db->tableExists('tbl_session_ratings') && $db->tableExists('tbl_conference_sessions')) ? $db->table('tbl_session_ratings r')
            ->select('s.sessions_name, COUNT(*) AS ratings_count, AVG(r.rating) AS average_rating')
            ->join('tbl_conference_sessions s', 's.sessions_id = r.sessions_id', 'left')->groupBy('r.sessions_id, s.sessions_name')->orderBy('ratings_count', 'DESC')->limit(10)->get()->getResultArray() : [];
        $viewedSessions = ($db->tableExists('tbl_session_viewers') && $db->tableExists('tbl_conference_sessions')) ? $db->table('tbl_session_viewers v')
            ->select('s.sessions_name, COUNT(*) AS viewer_count')
            ->join('tbl_conference_sessions s', 's.sessions_id = v.sessions_id', 'left')
            ->groupBy('v.sessions_id, s.sessions_name')->orderBy('viewer_count', 'DESC')->limit(10)->get()->getResultArray() : [];
        $exhibitors = $db->tableExists('tbl_exhibitors')
            ? $db->table('tbl_exhibitors')->select('company_name AS exhibitor_name, views, likes')->orderBy('views', 'DESC')->limit(10)->get()->getResultArray()
            : [];

        return module_view('Admin', 'analytics/index', compact('users', 'attendees', 'registered2026', 'ratings', 'averageRating', 'uniqueSessionViewers', 'countries', 'sessions', 'viewedSessions', 'exhibitors'));
    }
}
