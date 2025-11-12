<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 11:38
 */


namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Modules\MobileApp\Models\SponsorModel;

class SponsorsController extends BaseController
{
    public function index()
    {

        $model = new SponsorModel();
        $data['sponsors'] = $model->getAllSponsors();

        echo module_view('MobileApp', 'sponsors', $data);
    }
}
