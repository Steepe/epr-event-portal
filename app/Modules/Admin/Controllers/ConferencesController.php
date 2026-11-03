<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 03:03
 */

namespace App\Modules\Admin\Controllers;

use App\Controllers\BaseController;
use App\Modules\Admin\Models\ConferencesModel;
use App\Services\BunnyStreamService;
use CodeIgniter\Exceptions\PageNotFoundException;

class ConferencesController extends BaseController
{
    protected ConferencesModel $conferenceModel;

    public function __construct()
    {
        $this->conferenceModel = new ConferencesModel();
    }

    public function index()
    {
        $conferences = $this->conferenceModel
            ->orderBy('start_date', 'DESC')
            ->findAll();

        echo module_view('Admin', 'conferences/index', [
            'conferences' => $conferences,
            'pageTitle' => 'Manage Conferences',
        ]);
    }

    public function create()
    {
        echo module_view('Admin', 'conferences/create', [
            'pageTitle' => 'Create Conference',
        ]);
    }

    public function edit($id = null)
    {
        $conference = $this->conferenceModel->find($id);
        if (! $conference) {
            throw new PageNotFoundException('Conference not found.');
        }

        echo module_view('Admin', 'conferences/edit', [
            'conference' => $conference,
            'pageTitle' => 'Edit Conference',
        ]);
    }

    public function store(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($this->postExceedsUploadLimit()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'The selected video is larger than this server allows. No conference was created. Increase both upload_max_filesize and post_max_size before trying again.');
        }

        $title = trim((string)$this->request->getPost('title'));
        if ($title === '') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Conference title is required.');
        }

        try {
            $welcomeVideoUrl = $this->uploadWelcomeVideo();
        } catch (\RuntimeException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        $data = [
            'title'        => $title, // still mapped to DB field `name`
            'slug'        => url_title($title, '-', true) ?: uniqid('conf-'),
            'description' => $this->request->getPost('description'),
            'year'        => $this->request->getPost('year'),
            'is_paid'     => $this->request->getPost('is_paid') ? 1 : 0,
            'status'      => $this->request->getPost('status'),
            'days'        => $this->request->getPost('days'),
            'start_date'  => $this->request->getPost('start_date'),
            'end_date'    => $this->request->getPost('end_date'),
            'welcome_video_url' => $welcomeVideoUrl,
        ];

        // ✅ Enforce one-live rule
        if ($data['status'] === 'live') {
            $liveExists = $this->conferenceModel->where('status', 'live')->countAllResults();
            if ($liveExists > 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Another conference is already marked as live.');
            }
        }

// ✅ Handle icon upload
        $iconFile = $this->request->getFile('icon');
        if ($iconFile && $iconFile->isValid() && !$iconFile->hasMoved()) {
            $newName = $iconFile->getRandomName();
            $iconFile->move(FCPATH . 'uploads/conferences', $newName);
            $data['icon'] = $newName; // 👈 only filename stored in DB
        }

        $this->conferenceModel->insert($data);
        return redirect()
            ->to(site_url('admin/conferences'))
            ->with('success', 'Conference created successfully.');
    }

    public function update($id = null): \CodeIgniter\HTTP\RedirectResponse
    {
        $conference = $this->conferenceModel->find($id);
        if (! $conference) {
            throw new PageNotFoundException('Conference not found.');
        }

        if ($this->postExceedsUploadLimit()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'The selected video is larger than this server allows. No conference changes were saved. Increase both upload_max_filesize and post_max_size before trying again.');
        }

        $title = trim((string) $this->request->getPost('title'));
        if ($title === '') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Conference name is required. No conference changes were saved.');
        }

        $welcomeVideoUrl = (string) ($conference['welcome_video_url'] ?? '');
        try {
            $uploadedVideoUrl = $this->uploadWelcomeVideo();
            if ($uploadedVideoUrl !== null) {
                $welcomeVideoUrl = $uploadedVideoUrl;
            }
        } catch (\RuntimeException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        $data = [
            'title'        => $title,
            'description' => $this->request->getPost('description'),
            'year'        => $this->request->getPost('year'),
            'is_paid'     => $this->request->getPost('is_paid') ? 1 : 0,
            'status'      => $this->request->getPost('status'),
            'days'        => $this->request->getPost('days'),
            'start_date'  => $this->request->getPost('start_date'),
            'end_date'    => $this->request->getPost('end_date'),
            'welcome_video_url' => $welcomeVideoUrl ?: null,
        ];

        // ✅ Enforce one live conference rule (excluding current one)
        if ($data['status'] === 'live') {
            $liveExists = $this->conferenceModel
                ->where('status', 'live')
                ->where('conference_id !=', $id)
                ->countAllResults();

            if ($liveExists > 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Another conference is already marked as live.');
            }
        }

        // ✅ Handle icon upload (replace old one if uploaded)
        $iconFile = $this->request->getFile('icon');
        if ($iconFile && $iconFile->isValid() && !$iconFile->hasMoved()) {
            $newName = $iconFile->getRandomName();
            $iconFile->move(FCPATH . 'uploads/conferences', $newName);
            $data['icon'] = 'uploads/conferences/' . $newName;

            // Delete old icon if it exists
            if (!empty($conference['icon']) && file_exists(FCPATH . $conference['icon'])) {
                @unlink(FCPATH . $conference['icon']);
            }
        }

        $this->conferenceModel->update($id, $data);
        return redirect()
            ->to(site_url('admin/conferences'))
            ->with('success', 'Conference updated successfully.');
    }

    public function delete($id = null): \CodeIgniter\HTTP\RedirectResponse
    {
        $this->conferenceModel->delete($id);
        return redirect()->to(site_url('admin/conferences'))->with('success', 'Conference deleted.');
    }

    /**
     * @throws \RuntimeException
     */
    private function uploadWelcomeVideo(): ?string
    {
        $file = $this->request->getFile('welcome_video');
        if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        return (new BunnyStreamService())->uploadWelcomeVideo(
            $file,
            trim((string) $this->request->getPost('title')) . ' welcome video'
        );
    }

    private function postExceedsUploadLimit(): bool
    {
        $contentLength = (int) $this->request->getServer('CONTENT_LENGTH');
        $limit = $this->iniBytes((string) ini_get('post_max_size'));

        return $contentLength > 0 && $limit > 0 && $contentLength > $limit;
    }

    private function iniBytes(string $value): int
    {
        $value = trim($value);
        if ($value === '') {
            return 0;
        }

        $unit = strtolower(substr($value, -1));
        $bytes = (int) $value;

        return match ($unit) {
            'g' => $bytes * 1024 * 1024 * 1024,
            'm' => $bytes * 1024 * 1024,
            'k' => $bytes * 1024,
            default => $bytes,
        };
    }
}
