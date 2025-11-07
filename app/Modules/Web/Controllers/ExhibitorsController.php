<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 01/11/2025
 * Time: 21:18
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use App\Modules\Api\Models\TblExhibitorsModel;

class ExhibitorsController extends BaseController
{
    public function index()
    {
        $model = new TblExhibitorsModel();
        $data['exhibitors'] = $model->orderBy('company_name', 'ASC')->findAll();
        return module_view('Web', 'exhibitors', $data);
    }

    public function booth($id)
    {
        $model = new TblExhibitorsModel();
        $exhibitor = $model->find($id);

        if (!$exhibitor) {
            return redirect()->to(base_url('attendees/exhibitors'))->with('error', 'Exhibitor not found.');
        }

        // Increment views
        $model->update($id, ['views' => $exhibitor['views'] + 1]);

        $data['exhibitor'] = $exhibitor;
        return module_view('Web', 'exhibitor_booth', $data);
    }
}
