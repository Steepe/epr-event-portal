<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 08/11/2025
 * Time: 23:35
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;

class EnvisionController extends BaseController
{
    public function index()
    {
        // Load the Envision page
        return module_view('Web', 'envision');
    }
}
