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

    /**
     * The standalone list now lives inside the Networking Center.
     */
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        return redirect()->to(site_url('mobile/networking-center?tab=speakers'));
    }
}
