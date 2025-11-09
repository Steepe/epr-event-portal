<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 06:27
 */

namespace App\Modules\Admin\Controllers;

use App\Controllers\BaseController;
use App\Modules\Admin\Models\SpeakersModel;
use App\Modules\Admin\Models\ConferenceSessionsModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class SpeakersController extends BaseController
{
    protected SpeakersModel $speakerModel;
    protected ConferenceSessionsModel $sessionModel;

    public function __construct()
    {
        $this->speakerModel = new SpeakersModel();
        $this->sessionModel = new ConferenceSessionsModel();
    }

    public function index()
    {
        $speakers = $this->speakerModel->orderBy('speaker_name', 'ASC')->findAll();

        echo module_view('Admin', 'speakers/index', [
            'speakers' => $speakers,
            'pageTitle' => 'Speakers'
        ]);
    }

    public function create()
    {
        echo module_view('Admin', 'speakers/create', [
            'pageTitle' => 'Add Speaker'
        ]);
    }

    public function store()
    {
        $photo = $this->request->getFile('speaker_photo');
        $photoPath = null;

        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $newName = $photo->getRandomName();
            $photo->move(FCPATH . 'uploads/speakers/', $newName);
            $photoPath = $newName; // ✅ only filename in DB
        }

        $data = [
            'speaker_name'    => $this->request->getPost('speaker_name'),
            'speaker_title'   => $this->request->getPost('speaker_title'),
            'speaker_company' => $this->request->getPost('speaker_company'),
            'bio'             => $this->request->getPost('bio'),
            'speaker_photo'   => $photoPath,
        ];

        $this->speakerModel->insert($data);
        return redirect()->to(site_url('admin/speakers'))->with('success', 'Speaker added successfully.');
    }

    public function edit($id = null)
    {
        $speaker = $this->speakerModel->find($id);
        if (!$speaker) {
            throw new PageNotFoundException('Speaker not found');
        }

        echo module_view('Admin', 'speakers/edit', [
            'speaker' => $speaker,
            'pageTitle' => 'Edit Speaker'
        ]);
    }

    public function update($id = null)
    {
        $speaker = $this->speakerModel->find($id);
        if (!$speaker) {
            throw new PageNotFoundException('Speaker not found');
        }

        $photo = $this->request->getFile('speaker_photo');
        $photoPath = $speaker['speaker_photo'];

        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $newName = $photo->getRandomName();
            $photo->move(FCPATH . 'uploads/speakers/', $newName);
            $photoPath = $newName; // ✅ only filename in DB
        }

        $data = [
            'speaker_name'    => $this->request->getPost('speaker_name'),
            'speaker_title'   => $this->request->getPost('speaker_title'),
            'speaker_company' => $this->request->getPost('speaker_company'),
            'bio'             => $this->request->getPost('bio'),
            'speaker_photo'   => $photoPath,
        ];

        $this->speakerModel->update($id, $data);
        return redirect()->to(site_url('admin/speakers'))->with('success', 'Speaker updated successfully.');
    }

    public function delete($id = null)
    {
        $speaker = $this->speakerModel->find($id);
        if ($speaker && $speaker['speaker_photo'] && file_exists(FCPATH . $speaker['speaker_photo'])) {
            unlink(FCPATH . $speaker['speaker_photo']);
        }

        $this->speakerModel->delete($id);
        return redirect()->to(site_url('admin/speakers'))->with('success', 'Speaker deleted.');
    }
}
