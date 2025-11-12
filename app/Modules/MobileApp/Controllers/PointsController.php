<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 11:49
 */

namespace App\Modules\MobileApp\Controllers;

use App\Controllers\BaseController;
use App\Modules\MobileApp\Models\PointsGuideModel;

class PointsController extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('mobile/login'));
        }

        $model = new PointsGuideModel();
        $data['point_guides'] = $model->orderBy('points', 'DESC')->findAll();

        return view('App\Modules\MobileApp\Views\points', $data);
    }
}
