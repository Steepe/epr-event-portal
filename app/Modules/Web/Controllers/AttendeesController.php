<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 31/10/2025
 * Time: 00:08
 */

namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use App\Services\AttendeeDirectoryService;

class AttendeesController extends BaseController
{
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('attendees/login'));
        }

        $search = (string) $this->request->getGet('attendee_name');
        $letter = (string) $this->request->getGet('sort');
        $onlineOnly = $this->request->getGet('filter') === 'online';
        $page = max(1, (int) $this->request->getGet('page'));

        try {
            $data = (new AttendeeDirectoryService())->page(
                (int) ($session->get('user_id') ?: $session->get('attendee_id')),
                $search,
                $letter,
                $onlineOnly,
                $page
            );
        } catch (\Throwable $e) {
            log_message('error', '[AttendeesController] '.$e->getMessage());
            $data = [
                'attendees' => [],
                'total' => 0,
                'all_count' => 0,
                'online_count' => 0,
                'page' => 1,
                'per_page' => 60,
                'has_more' => false,
            ];
        }

        return module_view('Web', 'attendees_list', $data + [
            'page_title' => 'Attendees',
            'search_value' => $search,
            'letter_value' => $letter,
            'online_only' => $onlineOnly,
        ]);
    }

    public function profile(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $session = session();

        if (! $session->get('logged_in')) {
            return redirect()->to(base_url('attendees/login'));
        }

        $attendeeId = $session->get('attendee_id');

        $db = db_connect();
        $attendee = $db->table('tbl_attendees')
            ->where('attendee_id', $attendeeId)
            ->get()
            ->getRowArray();

        if (! $attendee) {
            return redirect()->back()->with('error', 'Unable to load profile details.');
        }

        return module_view('Web', 'profile', [
            'attendee' => $attendee
        ]);
    }

}
