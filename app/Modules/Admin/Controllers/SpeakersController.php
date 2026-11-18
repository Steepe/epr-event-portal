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
        try {
            $photoPath = $this->storePhoto();
        } catch (\RuntimeException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        $data = [
            'speaker_name'    => $this->request->getPost('speaker_name'),
            'speaker_email'    => $this->request->getPost('speaker_email'),
            'speaker_title'   => $this->request->getPost('speaker_title'),
            'speaker_company' => $this->request->getPost('speaker_company'),
            'bio'             => $this->request->getPost('bio'),
            'speaker_photo'   => $photoPath,
            'linkedin_url'    => $this->request->getPost('linkedin_url'),
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

        $photoPath = $speaker['speaker_photo'];

        try {
            $newPhotoPath = $this->storePhoto();
        } catch (\RuntimeException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }
        if ($newPhotoPath !== null) {
            $this->deleteStoredPhoto((string) $photoPath);
            $photoPath = $newPhotoPath;
        }

        $data = [
            'speaker_name'    => $this->request->getPost('speaker_name'),
            'speaker_email'    => $this->request->getPost('speaker_email'),
            'speaker_title'   => $this->request->getPost('speaker_title'),
            'speaker_company' => $this->request->getPost('speaker_company'),
            'bio'             => $this->request->getPost('bio'),
            'speaker_photo'   => $photoPath,
            'linkedin_url'    => $this->request->getPost('linkedin_url'),
        ];

        $this->speakerModel->update($id, $data);
        return redirect()->to(site_url('admin/speakers'))->with('success', 'Speaker updated successfully.');
    }

    public function delete($id = null)
    {
        $speaker = $this->speakerModel->find($id);
        if ($speaker) {
            $this->deleteStoredPhoto((string) ($speaker['speaker_photo'] ?? ''));
        }

        $this->speakerModel->delete($id);
        return redirect()->to(site_url('admin/speakers'))->with('success', 'Speaker deleted.');
    }

    /**
     * Stores a new image and returns its filename. A missing file is allowed;
     * invalid uploads are reported rather than silently discarded.
     */
    private function storePhoto(): ?string
    {
        if ($this->postExceedsUploadLimit()) {
            throw new \RuntimeException($this->serverUploadLimitMessage());
        }

        $photo = $this->request->getFile('speaker_photo');
        if (! $photo || $photo->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (! $photo->isValid()) {
            if ($photo->getError() === UPLOAD_ERR_INI_SIZE) {
                throw new \RuntimeException($this->serverUploadLimitMessage());
            }
            throw new \RuntimeException($this->uploadErrorMessage($photo->getError()));
        }
        if ($photo->hasMoved()) {
            throw new \RuntimeException('This speaker photo upload has already been processed. Please choose the image again.');
        }
        if (! in_array(strtolower((string) $photo->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true)) {
            throw new \RuntimeException('Upload a JPG, PNG, or WebP speaker photo.');
        }
        if ($photo->getSize() > 3 * 1024 * 1024) {
            throw new \RuntimeException('Speaker photos must be 3 MB or smaller.');
        }

        $directory = FCPATH . 'uploads/speakers/';
        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new \RuntimeException('The speaker photo folder could not be created. Please check server permissions.');
        }

        try {
            $name = $photo->getRandomName();
            $photo->move($directory, $name);
        } catch (\Throwable $exception) {
            log_message('error', 'Speaker photo upload failed: {message}', ['message' => $exception->getMessage()]);
            throw new \RuntimeException('The speaker photo could not be saved. Please try again or check server permissions.');
        }

        return $name;
    }

    private function deleteStoredPhoto(string $filename): void
    {
        $filename = basename($filename);
        if ($filename === '') {
            return;
        }

        $path = FCPATH . 'uploads/speakers/' . $filename;
        if (is_file($path)) {
            unlink($path);
        }
    }

    private function uploadErrorMessage(int $error): string
    {
        return match ($error) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'The speaker photo is too large. The maximum size is 3 MB.',
            UPLOAD_ERR_PARTIAL => 'The speaker photo upload was incomplete. Please try again.',
            UPLOAD_ERR_NO_TMP_DIR => 'The server is missing its temporary upload folder.',
            UPLOAD_ERR_CANT_WRITE => 'The server could not write the speaker photo. Please check server permissions.',
            UPLOAD_ERR_EXTENSION => 'A server extension stopped the speaker photo upload.',
            default => 'The speaker photo upload failed. Please try again.',
        };
    }

    private function postExceedsUploadLimit(): bool
    {
        $contentLength = (int) $this->request->getServer('CONTENT_LENGTH');
        $limit = $this->iniBytes((string) ini_get('post_max_size'));

        return $contentLength > 0 && $limit > 0 && $contentLength > $limit;
    }

    private function serverUploadLimitMessage(): string
    {
        $uploadLimit = (string) ini_get('upload_max_filesize');
        $postLimit = (string) ini_get('post_max_size');

        return "The server rejected this upload before the portal could check it (server limits: {$uploadLimit} per file and {$postLimit} per form). Please try again after the server upload limits have refreshed.";
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
