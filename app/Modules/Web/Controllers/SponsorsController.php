<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 31/10/2025
 * Time: 21:40
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;

class SponsorsController extends BaseController
{
    public function index()
    {
        try {
            $sponsors = db_connect()->table('tbl_sponsors')->get()->getResultArray();

            return module_view('Web', 'sponsors', ['sponsors' => $sponsors]);
        } catch (\Throwable $e) {
            log_message('error', 'Failed to load sponsors: ' . $e->getMessage());
            return module_view('Web', 'sponsors', ['sponsors' => []]);
        }
    }
}
