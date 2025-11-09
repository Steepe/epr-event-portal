<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 00:28
 */

namespace App\Modules\Admin\Controllers;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $data = [
            'admins_count'      => (int)$db->table('tbl_admins')->countAllResults(false),
            'users_count'       => (int)$db->table('tbl_users')->countAllResults(false),
            'conferences_count' => (int)$db->table('tbl_conferences')->countAllResults(false),
            'sessions_count'    => (int)$db->table('tbl_conference_sessions')->countAllResults(false),
        ];

        echo view('App\Modules\Admin\Views\dashboard', $data);
    }
}
