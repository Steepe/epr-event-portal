<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 29/10/2025
 * Time: 06:10
 */

namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use App\Modules\Api\Models\TblConferencesModel;
use CodeIgniter\API\ResponseTrait;
use App\Traits\AttendeeTrait; // ✅ Add Trait namespace

class AgendaController extends BaseController
{
    use ResponseTrait;
    use AttendeeTrait; // ✅ Use the AttendeeTrait for attendee/session/message info

    public function index()
    {
        $session = session();

        // 🧠 Ensure user is logged in
        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('attendees/login'));
        }

        // 🪪 Collect essential session data (your original logic — unchanged)
        $data = [
            'attendee_id' => $session->get('attendee_id'),
            'country'     => $session->get('reg_country') ?? 'Nigeria',
            'plan'        => $session->get('plan') ?? 1, // 1 = Free, 2 = Paid
            'timezone'    => $session->get('user_timezone') ?? 'Africa/Lagos',
        ];

        // 🎯 Optionally prefetch the current live conference
        $confModel = new TblConferencesModel();
        $data['conference'] = $confModel->where('is_live', 1)->first();

        // 🔥 Fetch data via the AttendeeTrait for the topbar and other views
        $data['global_attendee_details'] = $this->getAttendeeData();
        $data['attendee_sessions']       = $this->getAttendeeSessions();
        $data['unread_messages']         = $this->getUnreadMessagesCount();

        // ✅ Render the view — JS still handles API-driven content loading (no breakage)
        return module_view('Web', 'agenda', $data);
    }
}
