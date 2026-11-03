<?php

namespace App\Modules\Admin\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class AuditLogsController extends BaseController
{
    public function index()
    {
        if (session('admin_role') !== 'superadmin') {
            throw PageNotFoundException::forPageNotFound();
        }

        $event = trim((string) $this->request->getGet('event'));
        $adminId = (int) $this->request->getGet('admin_id');
        $builder = db_connect()->table('tbl_admin_activity_logs');

        if ($event !== '') {
            $builder->where('event', $event);
        }
        if ($adminId > 0) {
            $builder->where('admin_id', $adminId);
        }

        $activities = $builder->orderBy('activity_id', 'DESC')->limit(250)->get()->getResultArray();
        $admins = db_connect()->table('tbl_admins')->select('id, name, email')->orderBy('name', 'ASC')->get()->getResultArray();
        $events = db_connect()->table('tbl_admin_activity_logs')->select('event')->distinct()->orderBy('event', 'ASC')->get()->getResultArray();

        return module_view('Admin', 'audit_logs/index', [
            'activities' => $activities,
            'admins' => $admins,
            'events' => $events,
            'selectedEvent' => $event,
            'selectedAdminId' => $adminId,
            'pageTitle' => 'Admin Activity Log',
        ]);
    }
}
