<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 11/11/2025
 * Time: 11:51
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use App\Modules\Web\Models\PointsGuideModel;

class PointsController extends BaseController
{
    public function index()
    {
        $model = new PointsGuideModel();
        $data['point_guides'] = $model->findAll();

        return module_view('Web', 'earn_points', $data);
    }
}
