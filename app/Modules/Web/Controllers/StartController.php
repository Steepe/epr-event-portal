<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 23:35
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use App\Modules\Api\Models\ConferenceModel; // assuming same model used in API

class StartController extends BaseController
{
    public function index()
    {
        // Make sure user is logged in
        if (!session()->has('attendee_id')) {
            return redirect()->to(base_url('attendees/login'));
        }

        $conferenceModel = new ConferenceModel();

        // Fetch all conferences (live + past)
        $conferences = $conferenceModel
            ->whereIn('status', ['live', 'past'])
            ->orderBy('year', 'DESC')
            ->findAll();

        return module_view('Web', 'start',[
            'conferences' => $conferences
        ]);
    }
}
