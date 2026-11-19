<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 04:36
 */

namespace App\Modules\Admin\Controllers;

use App\Controllers\BaseController;
use App\Modules\Admin\Models\ConferenceSessionsModel;
use App\Modules\Admin\Models\ConferencesModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ConferenceSessionsController extends BaseController
{
    protected ConferenceSessionsModel $sessionModel;
    protected ConferencesModel $conferenceModel;

    public function __construct()
    {
        $this->sessionModel = new ConferenceSessionsModel();
        $this->conferenceModel = new ConferencesModel();
    }

    public function all()
    {
        $conferences = $this->conferenceModel
            ->select('tbl_conferences.*, COUNT(tbl_conference_sessions.sessions_id) AS session_count')
            ->join('tbl_conference_sessions', 'tbl_conference_sessions.conference_id = tbl_conferences.conference_id', 'left')
            ->groupBy('tbl_conferences.conference_id')
            ->orderBy('tbl_conferences.start_date', 'DESC')
            ->findAll();

        return module_view('Admin', 'sessions/all', [
            'conferences' => $conferences,
            'pageTitle' => 'Sessions',
        ]);
    }

    public function index($conference_id = null)
    {
        $conference = $this->conferenceModel->find($conference_id);
        if (!$conference) {
            throw new PageNotFoundException('Conference not found.');
        }

        $sessions = $this->sessionModel
            ->where('conference_id', $conference_id)
            ->orderBy('event_date', 'ASC')
            ->orderBy('start_time', 'ASC')
            ->orderBy('sessions_id', 'ASC')
            ->findAll();
        $sessionDateRange = $this->sessionDateRange($conference);
        $invalidSessionIds = [];
        if ($sessionDateRange !== null) {
            foreach ($sessions as $session) {
                $eventDate = (string) ($session['event_date'] ?? '');
                if ($eventDate < $sessionDateRange['start'] || $eventDate > $sessionDateRange['end']) {
                    $invalidSessionIds[] = (int) $session['sessions_id'];
                }
            }
        }

        echo module_view('Admin', 'sessions/index', [
            'conference' => $conference,
            'sessions' => $sessions,
            'sessionDateRange' => $sessionDateRange,
            'invalidSessionIds' => $invalidSessionIds,
            'pageTitle' => 'Sessions - ' . $conference['title']
        ]);
    }

    public function create($conference_id = null)
    {
        $conference = $this->conferenceModel->find($conference_id);
        if (! $conference) {
            throw new PageNotFoundException('Conference not found.');
        }

        // Check if this is a live conference
        $isLive = ($conference['status'] === 'live');

        // Only fetch speakers if live
        $speakers = [];
        if ($isLive) {
            $speakers = db_connect()->table('tbl_speakers')
                ->orderBy('speaker_name', 'ASC')
                ->get()
                ->getResultArray();
        }

        echo module_view('Admin', 'sessions/create', [
            'conference' => $conference,
            'speakers'   => $speakers,
            'isLive'     => $isLive,
            'sessionDateRange' => $this->sessionDateRange($conference),
            'pageTitle'  => 'Add Session to ' . $conference['title']
        ]);
    }

    public function store($conference_id = null)
    {
        $conference = $this->conferenceModel->find($conference_id);
        if (! $conference) {
            throw new PageNotFoundException('Conference not found.');
        }

        helper(['form', 'url']);
        $isLive = ($conference['status'] === 'live');
        $eventDate = trim((string) $this->request->getPost('event_date'));
        if ($dateError = $this->sessionDateError($conference, $eventDate)) {
            return redirect()->back()->withInput()->with('error', $dateError);
        }

        // Handle workbook upload
        $file = $this->request->getFile('workbook');
        $workbookPath = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/workbooks/', $newName);
            $workbookPath = 'uploads/workbooks/' . $newName;
        }

        $streamData = $this->streamData();
        if ($streamData === null) {
            return redirect()->back()->withInput()->with('error', 'Enter a valid Zoom meeting number for a Zoom session.');
        }

        // Insert session
        $data = [
            'conference_id' => $conference_id,
            'sessions_name' => $this->request->getPost('sessions_name'),
            'event_date'    => $eventDate,
            'start_time'    => $this->request->getPost('start_time'),
            'end_time'      => $this->request->getPost('end_time'),
            'access_level'  => $this->request->getPost('access_level'),
            'description'   => $this->request->getPost('description'),
            ...$streamData,
            'workbook'      => $workbookPath,
            'tags'          => $this->request->getPost('tags'),
        ];

        $this->sessionModel->insert($data);
        $sessionId = $this->sessionModel->getInsertID();

        // Handle speaker assignments (only for live conferences)
        if ($isLive && $this->request->getPost('speakers')) {
            $speakers = $this->request->getPost('speakers');
            $db = db_connect();
            $builder = $db->table('tbl_session_speakers');

            foreach ($speakers as $speakerId) {
                $builder->insert([
                    'sessions_id' => $sessionId,
                    'speaker_id'  => $speakerId
                ]);
            }
        }

        return redirect()->to(site_url('admin/conferences/' . $conference_id . '/sessions'))
            ->with('success', 'Session created successfully.');
    }

    public function edit($id = null)
    {
        $session = $this->sessionModel->find($id);
        if (! $session) {
            throw new PageNotFoundException('Session not found.');
        }

        $conference = $this->conferenceModel->find($session['conference_id']);
        if (! $conference) {
            throw new PageNotFoundException('Conference not found.');
        }

        $isLive = ($conference['status'] === 'live');
        $db = db_connect();

        // Fetch all speakers (only for live conferences)
        $speakers = [];
        if ($isLive) {
            $speakers = $db->table('tbl_speakers')
                ->orderBy('speaker_name', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Fetch already assigned speaker IDs
        $assignedSpeakers = [];
        $assigned = $db->table('tbl_session_speakers')
            ->where('sessions_id', $id)
            ->get()
            ->getResultArray();

        foreach ($assigned as $a) {
            $assignedSpeakers[] = $a['speaker_id'];
        }

        echo module_view('Admin', 'sessions/edit', [
            'session'          => $session,
            'conference'       => $conference,
            'speakers'         => $speakers,
            'assignedSpeakers' => $assignedSpeakers,
            'isLive'           => $isLive,
            'sessionDateRange' => $this->sessionDateRange($conference),
            'pageTitle'        => 'Edit Session - ' . $conference['title']
        ]);
    }

    public function update($id = null)
    {
        $session = $this->sessionModel->find($id);
        if (! $session) {
            throw new PageNotFoundException('Session not found.');
        }

        $conference = $this->conferenceModel->find($session['conference_id']);
        $isLive = ($conference['status'] === 'live');
        $eventDate = trim((string) $this->request->getPost('event_date'));
        if ($dateError = $this->sessionDateError($conference, $eventDate)) {
            return redirect()->back()->withInput()->with('error', $dateError);
        }

        $file = $this->request->getFile('workbook');
        $workbookPath = $session['workbook'];

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/workbooks/', $newName);
            $workbookPath = 'uploads/workbooks/' . $newName;
        }

        $streamData = $this->streamData();
        if ($streamData === null) {
            return redirect()->back()->withInput()->with('error', 'Enter a valid Zoom meeting number for a Zoom session.');
        }

        $data = [
            'sessions_name' => $this->request->getPost('sessions_name'),
            'event_date'    => $eventDate,
            'start_time'    => $this->request->getPost('start_time'),
            'end_time'      => $this->request->getPost('end_time'),
            'access_level'  => $this->request->getPost('access_level'),
            'description'   => $this->request->getPost('description'),
            ...$streamData,
            'workbook'      => $workbookPath,
            'tags'          => $this->request->getPost('tags'),
        ];

        $this->sessionModel->update($id, $data);

        // Reassign speakers (only for live conferences)
        if ($isLive) {
            $db = db_connect();
            $builder = $db->table('tbl_session_speakers');

            // clear existing assignments
            $builder->where('sessions_id', $id)->delete();

            // insert new ones
            $speakers = $this->request->getPost('speakers');
            if ($speakers && is_array($speakers)) {
                foreach ($speakers as $speakerId) {
                    $builder->insert([
                        'sessions_id' => $id,
                        'speaker_id'  => $speakerId
                    ]);
                }
            }
        }

        return redirect()->to(site_url('admin/conferences/' . $session['conference_id'] . '/sessions'))
            ->with('success', 'Session updated successfully.');
    }

    /**
     * Moves incorrectly dated sessions onto the closest date permitted by the
     * conference duration. This is deliberately an explicit admin action so
     * existing schedules are never silently rewritten.
     */
    public function normalizeDates($conferenceId = null): \CodeIgniter\HTTP\RedirectResponse
    {
        $conference = $this->conferenceModel->find($conferenceId);
        if (! $conference) {
            throw new PageNotFoundException('Conference not found.');
        }

        $range = $this->sessionDateRange($conference);
        if ($range === null) {
            return redirect()->to(site_url('admin/conferences/' . $conferenceId . '/sessions'))
                ->with('error', 'Set a valid conference start date and duration before aligning session dates.');
        }

        $db = db_connect();
        $sessions = $db->table('tbl_conference_sessions');
        $beforeCount = $db->table('tbl_conference_sessions')
            ->where('conference_id', $conferenceId)
            ->where('event_date <', $range['start'])
            ->countAllResults();
        $afterCount = $db->table('tbl_conference_sessions')
            ->where('conference_id', $conferenceId)
            ->where('event_date >', $range['end'])
            ->countAllResults();

        if ($beforeCount > 0) {
            $sessions->where('conference_id', $conferenceId)
                ->where('event_date <', $range['start'])
                ->update(['event_date' => $range['start']]);
        }
        if ($afterCount > 0) {
            $sessions->where('conference_id', $conferenceId)
                ->where('event_date >', $range['end'])
                ->update(['event_date' => $range['end']]);
        }

        $moved = $beforeCount + $afterCount;
        $message = $moved > 0
            ? $moved . ' session' . ($moved === 1 ? '' : 's') . ' moved into the configured conference date range.'
            : 'All session dates already match the configured conference duration.';

        return redirect()->to(site_url('admin/conferences/' . $conferenceId . '/sessions'))
            ->with('success', $message);
    }

    public function delete($id = null)
    {
        $session = $this->sessionModel->find($id);
        if (!$session) {
            throw new PageNotFoundException('Session not found.');
        }

        if (!empty($session['workbook']) && file_exists(FCPATH . 'uploads/workbooks/' . $session['workbook'])) {
            unlink(FCPATH . 'uploads/workbooks/' . $session['workbook']);
        }

        $this->sessionModel->delete($id);

        return redirect()
            ->to(site_url('admin/conferences/' . $session['conference_id'] . '/sessions'))
            ->with('success', 'Session deleted successfully.');
    }

    private function normaliseLivepushId(mixed $value): string
    {
        if (! is_string($value)) {
            return '';
        }

        $value = trim($value);

        if (preg_match('~watch\.livepush\.io/live/([A-Za-z0-9_-]+)~', $value, $matches)) {
            return $matches[1];
        }

        return preg_match('/^[A-Za-z0-9_-]+$/', $value) ? $value : '';
    }

    /**
     * @return array{start: string, end: string}|null
     */
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

    private function sessionDateError(array $conference, string $eventDate): ?string
    {
        $range = $this->sessionDateRange($conference);
        if ($range === null || ($eventDate >= $range['start'] && $eventDate <= $range['end'])) {
            return null;
        }

        $dateRange = $range['start'] === $range['end'] ? $range['start'] : $range['start'] . ' to ' . $range['end'];
        return 'This session must be scheduled within the conference dates: ' . $dateRange . ' (GMT / UTC+0).';
    }

    /**
     * Maps the selected delivery method to the dedicated live-session columns.
     */
    private function streamData(): ?array
    {
        $provider = $this->request->getPost('stream_provider') === 'zoom' ? 'zoom' : 'livepush';

        if ($provider === 'livepush') {
            return [
                'livepush_id'         => $this->normaliseLivepushId($this->request->getPost('livepush_id')),
                'stream_provider'      => 'livepush',
                'zoom_meeting_number'  => null,
                'zoom_passcode'        => null,
                'zoom_is_webinar'      => 0,
            ];
        }

        $meetingNumber = preg_replace('/\D+/', '', (string) $this->request->getPost('zoom_meeting_number'));
        if (! is_string($meetingNumber) || ! preg_match('/^\d{9,16}$/', $meetingNumber)) {
            return null;
        }

        return [
            'livepush_id'         => '',
            'stream_provider'      => 'zoom',
            'zoom_meeting_number'  => $meetingNumber,
            'zoom_passcode'        => mb_substr(trim((string) $this->request->getPost('zoom_passcode')), 0, 255),
            'zoom_is_webinar'      => $this->request->getPost('zoom_is_webinar') ? 1 : 0,
        ];
    }
}
