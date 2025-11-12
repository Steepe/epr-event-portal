<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 11/11/2025
 * Time: 16:09
 */

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Modules\MobileApp\Models\ConferenceModel;

class Home extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        $conferenceModel = new ConferenceModel();

        $conferences = $conferenceModel
            ->whereIn('status', ['live', 'past'])
            ->orderBy('year', 'DESC')
            ->findAll();

        return module_view('MobileApp', 'home', [
            'conferences' => $conferences
        ]);
    }
}
