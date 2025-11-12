<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 09:54
 */

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Modules\MobileApp\Models\SpeakersModel;

class SpeakersController extends BaseController
{
    protected SpeakersModel $speakersModel;

    public function __construct()
    {
        $this->speakersModel = new SpeakersModel();
    }

    public function index()
    {
        // 🔒 Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        $data = [
            'page_title' => 'Speakers',
            'speakers'   => $this->speakersModel->getAllSpeakers()
        ];

        return module_view('MobileApp', 'speakers_list', $data);
    }
}
