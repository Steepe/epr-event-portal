<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 03/11/2025
 * Time: 02:58
 */


namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;

class EmergenceBoothController extends BaseController
{
    public function index(): string
    {
        return module_view('Web', 'emergence_booth');
    }
}
