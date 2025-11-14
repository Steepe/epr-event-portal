<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 14/11/2025
 * Time: 17:29
 */

namespace App\Modules\Web\Controllers;


use App\Controllers\BaseController;

class CommunityController extends BaseController
{
    public function index(): string
    {
        // Load the Envision page
        return module_view('Web', 'communities');
    }

}