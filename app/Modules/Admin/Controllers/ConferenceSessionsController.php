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

    public function index($conference_id = null)
    {
        $conference = $this->conferenceModel->find($conference_id);
        if (!$conference) {
            throw new PageNotFoundException('Conference not found.');
        }

        $sessions = $this->sessionModel
            ->where('conference_id', $conference_id)
            ->orderBy('event_date', 'ASC')
            ->findAll();

        echo module_view('Admin', 'sessions/index', [
            'conference' => $conference,
            'sessions' => $sessions,
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

        // Handle workbook upload
        $file = $this->request->getFile('workbook');
        $workbookPath = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/workbooks/', $newName);
            $workbookPath = 'uploads/workbooks/' . $newName;
        }

        // Insert session
        $data = [
            'conference_id' => $conference_id,
            'sessions_name' => $this->request->getPost('sessions_name'),
            'event_date'    => $this->request->getPost('event_date'),
            'start_time'    => $this->request->getPost('start_time'),
            'end_time'      => $this->request->getPost('end_time'),
            'access_level'  => $this->request->getPost('access_level'),
            'description'   => $this->request->getPost('description'),
            'vimeo_id'      => $this->request->getPost('vimeo_id'),
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

        $file = $this->request->getFile('workbook');
        $workbookPath = $session['workbook'];

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/workbooks/', $newName);
            $workbookPath = 'uploads/workbooks/' . $newName;
        }

        $data = [
            'sessions_name' => $this->request->getPost('sessions_name'),
            'event_date'    => $this->request->getPost('event_date'),
            'start_time'    => $this->request->getPost('start_time'),
            'end_time'      => $this->request->getPost('end_time'),
            'access_level'  => $this->request->getPost('access_level'),
            'description'   => $this->request->getPost('description'),
            'vimeo_id'      => $this->request->getPost('vimeo_id'),
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
}
