<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 05:10
 */

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Modules\MobileApp\Models\ConferenceModel;

class Welcome extends BaseController
{
    /**
     * Start screen: choose between the live conference, past conferences and webinars.
     */
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        $conferenceModel = new ConferenceModel();

        $live = $conferenceModel
            ->where('status', 'live')
            ->orderBy('year', 'DESC')
            ->first();

        if (!empty($live['conference_id'])) {
            session()->set('live-conference-id', $live['conference_id']);
        }

        $past = $conferenceModel
            ->where('status', 'past')
            ->orderBy('start_date', 'DESC')
            ->findAll(3);

        return module_view('MobileApp', 'welcome', [
            'conference'      => $live,
            'pastConferences' => $past,
        ]);
    }
}
