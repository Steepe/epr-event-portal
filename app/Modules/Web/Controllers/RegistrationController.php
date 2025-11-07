<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 17:45
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\Exceptions\HTTPException;

class RegistrationController extends BaseController
{
    /**
     * Display the attendee registration form.
     *
     * Fetches country list from API endpoint /countries.
     */
    public function index(): string
    {
        $countriesResponse = api_get('countries');

        if ($countriesResponse['success']) {
            $data['countries'] = $countriesResponse['data']['data'] ?? [];
        } else {
            log_message('error', 'Country fetch failed: ' . $countriesResponse['error']);
            $data['countries'] = [];
        }

        return module_view('Web', 'register', $data);
    }
}
