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
        if (!$conference) {
            throw new PageNotFoundException('Conference not found.');
        }

        echo module_view('Admin', 'sessions/create', [
            'conference' => $conference,
            'pageTitle' => 'Add Session to ' . $conference['title']
        ]);
    }

    public function store($conference_id = null)
    {
        helper(['form', 'url']);

        $conference = $this->conferenceModel->find($conference_id);
        if (!$conference) {
            throw new PageNotFoundException('Conference not found.');
        }

        $file = $this->request->getFile('workbook');
        $workbookName = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Validate allowed file types
            $ext = strtolower($file->getClientExtension());
            if (!in_array($ext, ['pdf', 'doc', 'docx', 'ppt', 'pptx'])) {
                return redirect()->back()->with('error', 'Invalid workbook file type.');
            }

            $workbookName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/workbooks', $workbookName);
        }

        $data = [
            'conference_id' => $conference_id,
            'sessions_name' => $this->request->getPost('sessions_name'),
            'event_date' => $this->request->getPost('event_date'),
            'start_time' => $this->request->getPost('start_time'),
            'end_time' => $this->request->getPost('end_time'),
            'access_level' => $this->request->getPost('access_level'),
            'description' => $this->request->getPost('description'),
            'vimeo_id' => $this->request->getPost('vimeo_id'),
            'workbook' => $workbookName,
            'tags' => $this->request->getPost('tags'),
            'tags_meta' => $this->request->getPost('tags_meta'),
        ];

        $this->sessionModel->insert($data);

        return redirect()
            ->to(site_url('admin/conferences/' . $conference_id . '/sessions'))
            ->with('success', 'Session created successfully.');
    }

    public function edit($id = null)
    {
        $session = $this->sessionModel->find($id);
        if (!$session) {
            throw new PageNotFoundException('Session not found.');
        }

        $conference = $this->conferenceModel->find($session['conference_id']);

        echo module_view('Admin', 'sessions/edit', [
            'session' => $session,
            'conference' => $conference,
            'pageTitle' => 'Edit Session - ' . $conference['title']
        ]);
    }

    public function update($id = null)
    {
        $session = $this->sessionModel->find($id);
        if (!$session) {
            throw new PageNotFoundException('Session not found.');
        }

        $file = $this->request->getFile('workbook');
        $workbookName = $session['workbook'];

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = strtolower($file->getClientExtension());
            if (!in_array($ext, ['pdf', 'doc', 'docx', 'ppt', 'pptx'])) {
                return redirect()->back()->with('error', 'Invalid workbook file type.');
            }

            // Remove old workbook
            if (!empty($session['workbook']) && file_exists(FCPATH . 'uploads/workbooks/' . $session['workbook'])) {
                unlink(FCPATH . 'uploads/workbooks/' . $session['workbook']);
            }

            $workbookName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/workbooks', $workbookName);
        }

        $data = [
            'sessions_name' => $this->request->getPost('sessions_name'),
            'event_date' => $this->request->getPost('event_date'),
            'start_time' => $this->request->getPost('start_time'),
            'end_time' => $this->request->getPost('end_time'),
            'access_level' => $this->request->getPost('access_level'),
            'description' => $this->request->getPost('description'),
            'vimeo_id' => $this->request->getPost('vimeo_id'),
            'workbook' => $workbookName,
            'tags' => $this->request->getPost('tags'),
            'tags_meta' => $this->request->getPost('tags_meta'),
        ];

        $this->sessionModel->update($id, $data);

        return redirect()
            ->to(site_url('admin/conferences/' . $session['conference_id'] . '/sessions'))
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
