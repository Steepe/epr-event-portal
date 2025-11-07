<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 31/10/2025
 * Time: 20:55
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;

class PastConferencesController extends BaseController
{
    public function index()
    {
        $data = [
            'page_title' => 'Past Conferences'
        ];

        return module_view('Web', 'past_conferences', $data);
    }
}
